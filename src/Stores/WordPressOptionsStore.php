<?php

namespace Ksfraser\Prefs\Stores;

use Ksfraser\ModulesDAO\Stores\KeyValue\WordPressOptionsStore as DaoWordPressOptionsStore;

/**
 * WordPress-backed preferences store using options.
 */
class WordPressOptionsStore extends AbstractDelegatingStore
{
    public function __construct(string $optionPrefix = '', bool $autoload = false)
    {
        parent::__construct(
            new ModulesDaoKeyValueStoreAdapter(
                new DaoWordPressOptionsStore($optionPrefix, $autoload)
            )
        );
    }
}
