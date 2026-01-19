<?php

namespace Ksfraser\Prefs\Schema;

class PrefsSchemaRegistry
{
    /** @var PrefsSchema */
    private $schema;

    public function __construct(?PrefsSchema $schema = null)
    {
        $this->schema = $schema ?? new PrefsSchema();
    }

    public function getSchema(): PrefsSchema
    {
        return $this->schema;
    }

    public function addSchema(PrefsSchema $schema): self
    {
        foreach ($schema->all() as $def) {
            $this->schema->add($def);
        }
        return $this;
    }

    /**
     * @param PrefsSchemaProviderInterface[] $providers
     */
    public function addProviders(array $providers): self
    {
        foreach ($providers as $provider) {
            $this->addSchema($provider->getSchema());
        }
        return $this;
    }
}
