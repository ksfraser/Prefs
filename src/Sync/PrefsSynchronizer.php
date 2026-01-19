<?php

namespace Ksfraser\Prefs\Sync;

use Ksfraser\Prefs\Contracts\PrefsStoreInterface;
use RuntimeException;

class PrefsSynchronizer
{
    /**
     * Sync key/value pairs from one store to another.
     *
     * If $keys is provided, those keys will be copied (using get/set).
     * Otherwise, the source store's all($prefix) will be used.
     *
     * @param string[]|null $keys
     */
    public function sync(PrefsStoreInterface $from, PrefsStoreInterface $to, ?array $keys = null, ?string $prefix = null): int
    {
        if (!$from->isAvailable()) {
            throw new RuntimeException('Source store is not available');
        }
        if (!$to->isAvailable()) {
            throw new RuntimeException('Target store is not available');
        }

        $count = 0;

        if ($keys !== null) {
            foreach ($keys as $key) {
                $key = (string)$key;
                if ($key === '') {
                    continue;
                }
                $value = $from->get($key, null);
                $to->set($key, $value);
                $count++;
            }
            return $count;
        }

        $all = $from->all($prefix);
        foreach ($all as $k => $v) {
            $k = (string)$k;
            if ($k === '') {
                continue;
            }
            $to->set($k, $v);
            $count++;
        }

        return $count;
    }

    /**
     * Copy a specific set of keys from one store to another.
     *
     * @param string[] $keys
     */
    public function copyKeys(PrefsStoreInterface $from, PrefsStoreInterface $to, array $keys): int
    {
        return $this->sync($from, $to, $keys, null);
    }

    /**
     * Delete a set of keys from a store.
     *
     * @param string[] $keys
     */
    public function deleteKeys(PrefsStoreInterface $store, array $keys): int
    {
        if (!$store->isAvailable()) {
            throw new RuntimeException('Store is not available');
        }

        $count = 0;
        foreach ($keys as $key) {
            $key = (string)$key;
            if ($key === '') {
                continue;
            }
            if ($store->has($key)) {
                $store->delete($key);
                $count++;
            }
        }
        return $count;
    }
}
