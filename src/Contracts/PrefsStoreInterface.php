<?php

namespace Ksfraser\Prefs\Contracts;

/**
 * Storage backend for preferences.
 */
interface PrefsStoreInterface
{
    /**
     * Whether this store can operate in the current runtime.
     */
    public function isAvailable(): bool;

    /**
     * Whether a key exists in this store.
     */
    public function has(string $key): bool;

    /**
     * Get a stored value.
     *
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Set a stored value.
     *
     * @param mixed $value
     */
    public function set(string $key, $value): void;

    /**
     * Delete a stored value.
     */
    public function delete(string $key): void;

    /**
     * Return all keys/values. If prefix is provided, only return keys starting with it.
     *
     * @return array<string,mixed>
     */
    public function all(?string $prefix = null): array;
}
