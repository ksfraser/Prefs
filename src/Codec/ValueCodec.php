<?php

namespace Ksfraser\Prefs\Codec;

use Ksfraser\ModulesDAO\Codec\ValueCodec as DaoValueCodec;

/**
 * Encodes values as strings for storage backends.
 */
class ValueCodec
{
    /**
     * @param mixed $value
     */
    public static function encode($value): string
    {
        return DaoValueCodec::encode($value);
    }

    /**
     * @param string|null $raw
     * @param mixed $default
     * @return mixed
     */
    public static function decode($raw, $default = null)
    {
        return DaoValueCodec::decode($raw, $default);
    }
}
