<?php

namespace Ksfraser\Prefs;

use Ksfraser\Prefs\Contracts\PrefsStoreInterface;

/**
 * Convenience wrapper around a PrefsStoreInterface.
 */
class Prefs
{
    /** @var PrefsStoreInterface */
    private $store;

    public function __construct(PrefsStoreInterface $store)
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

    /**
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->store->get($key, $default);
    }

    /**
     * @param mixed $value
     */
    public function set(string $key, $value): void
    {
        $this->store->set($key, $value);
    }

    public function delete(string $key): void
    {
        $this->store->delete($key);
    }

    /**
     * @return array<string,mixed>
     */
    public function all(?string $prefix = null): array
    {
        return $this->store->all($prefix);
    }

    public function getBool(string $key, bool $default = false): bool
    {
        $value = $this->get($key, null);
        if ($value === null) {
            return $default;
        }
        if (is_bool($value)) {
            return $value;
        }
        $value = strtolower(trim((string)$value));
        return in_array($value, ['1', 'true', 't', 'yes', 'y', 'on'], true);
    }

    public function getInt(string $key, int $default = 0): int
    {
        $value = $this->get($key, null);
        if ($value === null || $value === '') {
            return $default;
        }
        return (int)$value;
    }

    public function getString(string $key, string $default = ''): string
    {
        $value = $this->get($key, null);
        if ($value === null) {
            return $default;
        }
        return (string)$value;
    }
}
