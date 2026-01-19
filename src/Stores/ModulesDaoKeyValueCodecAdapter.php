<?php

namespace Ksfraser\Prefs\Stores;

use Ksfraser\ModulesDAO\Codec\ValueCodec;
use Ksfraser\Prefs\Contracts\PrefsStoreInterface;

/**
 * Adapter for ModulesDAO KV stores that persist raw strings.
 *
 * Applies Prefs' ValueCodec on the way in/out.
 */
class ModulesDaoKeyValueCodecAdapter extends \Ksfraser\Prefs\Stores\AbstractModulesDaoKeyValueAdapter
{
    public function get(string $key, $default = null)
    {
        $rawDefault = ValueCodec::encode($default);
        $raw = $this->store->get($key, $rawDefault);

        // If the underlying store returns a non-string, just pass it through.
        if (!is_string($raw) && $raw !== null) {
            return $raw;
        }

        return ValueCodec::decode($raw, $default);
    }

    public function set(string $key, $value): void
    {
        $this->store->set($key, ValueCodec::encode($value));
    }

    public function all(?string $prefix = null): array
    {
        $raw = $this->store->all($prefix);
        $out = [];

        foreach ($raw as $k => $v) {
            if (!is_string($k) || $k === '') {
                continue;
            }
            if (!is_string($v) && $v !== null) {
                $out[$k] = $v;
                continue;
            }
            $out[$k] = ValueCodec::decode($v, null);
        }

        return $out;
    }
}
