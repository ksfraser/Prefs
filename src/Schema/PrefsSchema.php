<?php

namespace Ksfraser\Prefs\Schema;

use Ksfraser\Prefs\Contracts\PrefsStoreInterface;

class PrefsSchema
{
    /** @var PrefDefinition[] */
    private $defs = [];

    public function add(PrefDefinition $def): self
    {
        $this->defs[$def->getKey()] = $def;
        return $this;
    }

    /**
     * Convenience helper.
     *
     * @param mixed $defaultValue
     */
    public function addKey(string $key, $defaultValue = null, bool $required = false, ?string $description = null): self
    {
        return $this->add(new PrefDefinition($key, $defaultValue, $required, $description));
    }

    /**
     * @return PrefDefinition[] keyed by key
     */
    public function all(): array
    {
        return $this->defs;
    }

    /**
     * @return string[]
     */
    public function keys(): array
    {
        return array_keys($this->defs);
    }

    /**
     * @return PrefDefinition[]
     */
    public function required(): array
    {
        return array_filter($this->defs, static function (PrefDefinition $d): bool {
            return $d->isRequired();
        });
    }

    public function get(string $key): ?PrefDefinition
    {
        return $this->defs[$key] ?? null;
    }

    /**
     * Apply defaults into a store (only sets when missing).
     */
    public function applyDefaults(PrefsStoreInterface $store): int
    {
        $count = 0;
        foreach ($this->defs as $key => $def) {
            if (!$store->has($key)) {
                $store->set($key, $def->getDefaultValue());
                $count++;
            }
        }
        return $count;
    }
}
