<?php

namespace Ksfraser\Prefs\Manager;

use Ksfraser\ModulesDAO\Factory\KeyValueStoreFactory as DaoKeyValueStoreFactory;
use Ksfraser\Prefs\Config\IniConfig;
use Ksfraser\Prefs\Contracts\PrefsStoreInterface;
use Ksfraser\Prefs\Stores\ModulesDaoKeyValueCodecAdapter;
use Ksfraser\Prefs\Stores\ModulesDaoKeyValueStoreAdapter;
use Ksfraser\Prefs\Schema\PrefsSchema;
use Ksfraser\Prefs\Sync\PrefsSynchronizer;
use RuntimeException;

/**
 * Loads the active prefs store from an INI file and supports sync/switch.
 */
class PrefsStoreManager
{
    /** @var IniConfig */
    private $config;

    /** @var DaoKeyValueStoreFactory */
    private $daoFactory;

    /** @var PrefsSynchronizer */
    private $sync;

    /** @var PrefsSchema|null */
    private $schema;

    public function __construct(
        IniConfig $config,
        ?DaoKeyValueStoreFactory $daoFactory = null,
        ?PrefsSynchronizer $sync = null,
        ?PrefsSchema $schema = null
    )
    {
        $this->config = $config;
        $this->daoFactory = $daoFactory ?? new DaoKeyValueStoreFactory();
        $this->sync = $sync ?? new PrefsSynchronizer();
        $this->schema = $schema;
    }

    public function getSchema(): ?PrefsSchema
    {
        return $this->schema;
    }

    /**
     * @return array{type:string,codec?:bool}+array<string,mixed>
     */
    public function getCurrentDaoConfig(): array
    {
        $type = (string)$this->config->get('prefs', 'store_type', '');
        if ($type === '') {
            throw new RuntimeException('prefs.store_type is not set');
        }

        $section = $type;
        $sectionData = $this->config->all()[$section] ?? [];
        if (!is_array($sectionData)) {
            $sectionData = [];
        }

        $codec = $this->defaultCodecForType($type);
        $codecOverride = $this->config->get('prefs', 'codec', null);
        if ($codecOverride !== null) {
            $codec = filter_var($codecOverride, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($codec === null) {
                $codec = $this->defaultCodecForType($type);
            }
        }

        return array_merge(['type' => $type, 'codec' => $codec], $sectionData);
    }

    public function getCurrentStore(): PrefsStoreInterface
    {
        $daoConfig = $this->getCurrentDaoConfig();
        return $this->createPrefsStoreFromDaoConfig($daoConfig);
    }

    /**
     * @param array{type:string,codec?:bool}+array<string,mixed> $daoConfig
     */
    public function createPrefsStoreFromDaoConfig(array $daoConfig): PrefsStoreInterface
    {
        $codec = isset($daoConfig['codec']) ? (bool)$daoConfig['codec'] : $this->defaultCodecForType((string)$daoConfig['type']);

        // ModulesDAO factory expects 'type' and its own keys.
        $daoStore = $this->daoFactory->create($daoConfig);

        return $codec
            ? new ModulesDaoKeyValueCodecAdapter($daoStore)
            : new ModulesDaoKeyValueStoreAdapter($daoStore);
    }

    /**
     * Sync current store to a target store defined by config.
     *
     * @param array{type:string,codec?:bool}+array<string,mixed> $targetDaoConfig
     * @param string[]|null $keys
     */
    public function syncTo(array $targetDaoConfig, ?array $keys = null, ?string $prefix = null): int
    {
        $from = $this->getCurrentStore();
        $to = $this->createPrefsStoreFromDaoConfig($targetDaoConfig);

        if ($keys === null) {
            $configuredKeys = $this->config->get('sync', 'keys', null);
            if (is_array($configuredKeys)) {
                $keys = array_values(array_map('strval', $configuredKeys));
            }
        }

        return $this->sync->sync($from, $to, $keys, $prefix);
    }

    /**
     * Apply schema defaults to the current store.
     */
    public function applyDefaultsToCurrent(): int
    {
        if ($this->schema === null) {
            return 0;
        }
        return $this->schema->applyDefaults($this->getCurrentStore());
    }

    /**
     * Apply schema defaults to a target store config.
     *
     * @param array{type:string,codec?:bool}+array<string,mixed> $targetDaoConfig
     */
    public function applyDefaultsToTarget(array $targetDaoConfig): int
    {
        if ($this->schema === null) {
            return 0;
        }
        return $this->schema->applyDefaults($this->createPrefsStoreFromDaoConfig($targetDaoConfig));
    }

    /**
     * Switch the active store: sync current -> target, then update INI.
     *
     * @param array{type:string,codec?:bool}+array<string,mixed> $targetDaoConfig
     * @param string[]|null $keys
     */
    public function switchTo(array $targetDaoConfig, ?array $keys = null, ?string $prefix = null): int
    {
        $count = $this->syncTo($targetDaoConfig, $keys, $prefix);

        $type = (string)$targetDaoConfig['type'];
        $codec = isset($targetDaoConfig['codec']) ? (bool)$targetDaoConfig['codec'] : $this->defaultCodecForType($type);

        $this->config->set('prefs', 'store_type', $type);
        $this->config->set('prefs', 'codec', $codec);

        // Persist store-specific settings into the section named after the type.
        $section = $type;
        $sectionData = $targetDaoConfig;
        unset($sectionData['type'], $sectionData['codec']);
        $this->config->setSection($section, $sectionData);

        $this->config->save();
        $this->config->reload();

        return $count;
    }

    private function defaultCodecForType(string $type): bool
    {
        // WordPress options already handle arrays/objects; avoid changing representation.
        if ($type === 'wp_options') {
            return false;
        }

        // Most other backends effectively store strings.
        return true;
    }
}
