<?php

namespace Ksfraser\Prefs\Stores;

use Ksfraser\Prefs\Contracts\PrefsStoreInterface;
use RuntimeException;

/**
 * Tries multiple stores in order.
 * - Reads: first store where key exists.
 * - Writes: first available store.
 * - Deletes: all available stores.
 */
class CompositeStore implements PrefsStoreInterface
{
    /** @var PrefsStoreInterface[] */
    private $stores;

    /**
     * @param PrefsStoreInterface[] $stores
     */
    public function __construct(array $stores)
    {
        $this->stores = $stores;
    }

    public function isAvailable(): bool
    {
        foreach ($this->stores as $store) {
            if ($store->isAvailable()) {
                return true;
            }
        }
        return false;
    }

    public function has(string $key): bool
    {
        foreach ($this->stores as $store) {
            if (!$store->isAvailable()) {
                continue;
            }
            if ($store->has($key)) {
                return true;
            }
        }
        return false;
    }

    public function get(string $key, $default = null)
    {
        foreach ($this->stores as $store) {
            if (!$store->isAvailable()) {
                continue;
            }
            if ($store->has($key)) {
                return $store->get($key, $default);
            }
        }
        return $default;
    }

    public function set(string $key, $value): void
    {
        foreach ($this->stores as $store) {
            if (!$store->isAvailable()) {
                continue;
            }
            $store->set($key, $value);
            return;
        }

        throw new RuntimeException('No available prefs store for set()');
    }

    public function delete(string $key): void
    {
        $deleted = false;
        foreach ($this->stores as $store) {
            if (!$store->isAvailable()) {
                continue;
            }
            if ($store->has($key)) {
                $store->delete($key);
                $deleted = true;
            }
        }

        if (!$deleted) {
            // no-op
        }
    }

    public function all(?string $prefix = null): array
    {
        $out = [];
        foreach ($this->stores as $store) {
            if (!$store->isAvailable()) {
                continue;
            }
            $out = array_merge($out, $store->all($prefix));
        }
        return $out;
    }
}
