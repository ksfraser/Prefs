<?php

namespace Ksfraser\Prefs\Stores;

use Ksfraser\ModulesDAO\Stores\KeyValue\FrontAccountingSysPrefsStore as DaoFrontAccountingSysPrefsStore;

/**
 * FrontAccounting system preferences store (best-effort).
 *
 * This wrapper is intentionally conservative: FrontAccounting core functions
 * differ between versions. If setters are not available, set() throws.
 */
class FrontAccountingSysPrefsStore extends AbstractDelegatingStore
{
    public function __construct()
    {
        parent::__construct(
            new ModulesDaoKeyValueStoreAdapter(
                new DaoFrontAccountingSysPrefsStore()
            )
        );
    }
}
