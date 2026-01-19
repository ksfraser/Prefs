<?php

namespace Ksfraser\Prefs\Schema;

interface PrefsSchemaProviderInterface
{
    public function getSchema(): PrefsSchema;
}
