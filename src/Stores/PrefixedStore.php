<?php

namespace Ksfraser\Prefs\Stores;

use Ksfraser\Prefs\Contracts\PrefsStoreInterface;

/**
 * Adds a prefix/namespace to all keys.
 */
class PrefixedStore implements PrefsStoreInterface
{
    /** @var PrefsStoreInterface */
    private $inner;

    /** @var string */
    private $prefix;

    public function __construct(PrefsStoreInterface $inner, string $prefix)
    {
        $this->inner = $inner;
        $this->prefix = $prefix;
    }

    public function isAvailable(): bool
    {
        return $this->inner->isAvailable();
    }

    public function has(string $key): bool
    {
        return $this->inner->has($this->prefix . $key);
    }

    public function get(string $key, $default = null)
    {
        return $this->inner->get($this->prefix . $key, $default);
    }

    public function set(string $key, $value): void
    {
        $this->inner->set($this->prefix . $key, $value);
    }

    public function delete(string $key): void
    {
        $this->inner->delete($this->prefix . $key);
    }

    public function all(?string $prefix = null): array
    {
        $fullPrefix = $this->prefix . ($prefix ?? '');
        $all = $this->inner->all($fullPrefix);

        $out = [];
        foreach ($all as $k => $v) {
            if (strncmp($k, $this->prefix, strlen($this->prefix)) === 0) {
                $outKey = substr($k, strlen($this->prefix));
                $out[$outKey] = $v;
            }
        }
        return $out;
    }
}
