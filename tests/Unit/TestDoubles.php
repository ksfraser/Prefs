<?php

namespace Ksfraser\Prefs\Tests\Unit;

use Ksfraser\ModulesDAO\Contracts\KeyValueStoreInterface as DaoKeyValueStoreInterface;
use Ksfraser\Prefs\Contracts\PrefsStoreInterface;

class ArrayPrefsStore implements PrefsStoreInterface
{
    /** @var array<string,mixed> */
    private array $data;

    /** @param array<string,mixed> $data */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function isAvailable(): bool
    {
        return true;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    public function get(string $key, $default = null)
    {
        return array_key_exists($key, $this->data) ? $this->data[$key] : $default;
    }

    public function set(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    public function delete(string $key): void
    {
        unset($this->data[$key]);
    }

    public function all(?string $prefix = null): array
    {
        if ($prefix === null) {
            return $this->data;
        }

        $out = [];
        foreach ($this->data as $k => $v) {
            if (strncmp($k, $prefix, strlen($prefix)) === 0) {
                $out[$k] = $v;
            }
        }
        return $out;
    }
}

class ArrayDaoStore implements DaoKeyValueStoreInterface
{
    /** @var array<string,mixed> */
    private array $data;

    /** @param array<string,mixed> $data */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function isAvailable(): bool
    {
        return true;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    public function get(string $key, $default = null)
    {
        return array_key_exists($key, $this->data) ? $this->data[$key] : $default;
    }

    public function set(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    public function delete(string $key): void
    {
        unset($this->data[$key]);
    }

    public function all(?string $prefix = null): array
    {
        if ($prefix === null) {
            return $this->data;
        }

        $out = [];
        foreach ($this->data as $k => $v) {
            if (strncmp($k, $prefix, strlen($prefix)) === 0) {
                $out[$k] = $v;
            }
        }
        return $out;
    }
}
