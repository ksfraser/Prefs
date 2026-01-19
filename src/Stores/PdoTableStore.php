<?php

namespace Ksfraser\Prefs\Stores;

use PDO;
use Ksfraser\ModulesDAO\Stores\KeyValue\PdoTableStore as DaoPdoTableStore;

/**
 * Generic DB-table store using PDO.
 *
 * Expects a table with columns: pref_name (PK/unique), pref_value.
 */
class PdoTableStore extends AbstractDelegatingStore
{
    public function __construct(PDO $pdo, string $table, string $nameCol = 'pref_name', string $valueCol = 'pref_value')
    {
        parent::__construct(
            new ModulesDaoKeyValueCodecAdapter(
                new DaoPdoTableStore($pdo, $table, $nameCol, $valueCol)
            )
        );
    }
}
