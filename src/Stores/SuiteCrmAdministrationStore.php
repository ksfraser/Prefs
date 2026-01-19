<?php

namespace Ksfraser\Prefs\Stores;

use Ksfraser\ModulesDAO\Stores\KeyValue\SuiteCrmAdministrationStore as DaoSuiteCrmAdministrationStore;

/**
 * SuiteCRM-backed preferences store.
 *
 * Uses the Administration bean (SugarCRM/SuiteCRM pattern):
 * - retrieveSettings($category, true)
 * - saveSetting($category, $name, $value)
 */
class SuiteCrmAdministrationStore extends AbstractDelegatingStore
{
    public function __construct(string $category = 'ksf')
    {
        parent::__construct(
            new ModulesDaoKeyValueStoreAdapter(
                new DaoSuiteCrmAdministrationStore($category)
            )
        );
    }
}
