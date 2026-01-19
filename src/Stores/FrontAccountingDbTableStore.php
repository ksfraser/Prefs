<?php

namespace Ksfraser\Prefs\Stores;

use Ksfraser\ModulesDAO\Stores\KeyValue\FrontAccountingDbTableStore as DaoFrontAccountingDbTableStore;

/**
 * FrontAccounting-backed DB table store.
 *
 * Uses FrontAccounting db_* helpers and TB_PREF.
 */
class FrontAccountingDbTableStore extends AbstractDelegatingStore
{
    public function __construct(string $table, string $nameCol = 'pref_name', string $valueCol = 'pref_value')
    {
        parent::__construct(
            new ModulesDaoKeyValueCodecAdapter(
                new DaoFrontAccountingDbTableStore($table, $nameCol, $valueCol)
            )
        );
    }
}
