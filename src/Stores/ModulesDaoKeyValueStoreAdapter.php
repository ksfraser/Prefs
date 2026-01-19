<?php

namespace Ksfraser\Prefs\Stores;

use Ksfraser\Prefs\Contracts\PrefsStoreInterface;

/**
 * Adapter to let a ModulesDAO key/value store be used as a Prefs store.
 */
class ModulesDaoKeyValueStoreAdapter extends \Ksfraser\Prefs\Stores\AbstractModulesDaoKeyValueAdapter
{
    public function get(string $key, $default = null)
    {
        return $this->store->get($key, $default);
    }

    public function set(string $key, $value): void
    {
        $this->store->set($key, $value);
    }

    public function all(?string $prefix = null): array
    {
        return $this->store->all($prefix);
    }
}
