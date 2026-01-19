<?php

namespace Ksfraser\Prefs\Schema;

/**
 * Global schema registry for apps that want a simple integration point.
 *
 * Libraries/modules can call GlobalPrefsSchemaRegistry::addSchema() during bootstrap.
 */
class GlobalPrefsSchemaRegistry
{
    /** @var PrefsSchemaRegistry|null */
    private static $registry;

    public static function registry(): PrefsSchemaRegistry
    {
        if (self::$registry === null) {
            self::$registry = new PrefsSchemaRegistry();
        }
        return self::$registry;
    }

    public static function addSchema(PrefsSchema $schema): void
    {
        self::registry()->addSchema($schema);
    }

    public static function addProvider(PrefsSchemaProviderInterface $provider): void
    {
        self::registry()->addSchema($provider->getSchema());
    }

    public static function getSchema(): PrefsSchema
    {
        return self::registry()->getSchema();
    }
}
