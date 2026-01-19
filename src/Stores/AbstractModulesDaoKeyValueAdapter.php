<?php

namespace Ksfraser\Prefs\Stores;

use Ksfraser\ModulesDAO\Contracts\KeyValueStoreInterface as DaoKeyValueStoreInterface;
use Ksfraser\Prefs\Contracts\PrefsStoreInterface;

/**
 * Base adapter to expose a ModulesDAO key/value store as a Prefs store.
 */
abstract class AbstractModulesDaoKeyValueAdapter implements PrefsStoreInterface
{
    protected DaoKeyValueStoreInterface $store;

    public function __construct(DaoKeyValueStoreInterface $store)
    {
        $this->store = $store;
    }

    public function isAvailable(): bool
    {
        return $this->store->isAvailable();
    }

    public function has(string $key): bool
    {
        return $this->store->has($key);
    }

    public function delete(string $key): void
    {
        $this->store->delete($key);
    }
}
