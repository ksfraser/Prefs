<?php

namespace Ksfraser\Prefs\Stores;

use Ksfraser\Prefs\Contracts\PrefsStoreInterface;

/**
 * Base store that delegates PrefsStoreInterface methods to an inner store.
 */
abstract class AbstractDelegatingStore implements PrefsStoreInterface
{
    protected PrefsStoreInterface $inner;

    public function __construct(PrefsStoreInterface $inner)
    {
        $this->inner = $inner;
    }

    public function isAvailable(): bool
    {
        return $this->inner->isAvailable();
    }

    public function has(string $key): bool
    {
        return $this->inner->has($key);
    }

    public function get(string $key, $default = null)
    {
        return $this->inner->get($key, $default);
    }

    public function set(string $key, $value): void
    {
        $this->inner->set($key, $value);
    }

    public function delete(string $key): void
    {
        $this->inner->delete($key);
    }

    public function all(?string $prefix = null): array
    {
        return $this->inner->all($prefix);
    }
}
