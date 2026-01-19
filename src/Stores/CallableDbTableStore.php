<?php

namespace Ksfraser\Prefs\Stores;

use Ksfraser\ModulesDAO\Stores\KeyValue\CallableDbTableStore as DaoCallableDbTableStore;

/**
 * A generic DB-table store using injected callables.
 *
 * This is useful for platforms that provide their own DB layer (e.g. FrontAccounting).
 *
 * Callables:
 * - query(string $sql): mixed
 * - fetch(mixed $result): array|false
 * - escape(string $value): string
 * - tablePrefix(): string
 */
class CallableDbTableStore extends AbstractDelegatingStore
{
    /**
     * @param callable $query
     * @param callable $fetch
     * @param callable $escape
     * @param callable $tablePrefix
     */
    public function __construct(callable $query, callable $fetch, callable $escape, callable $tablePrefix, string $table, string $nameCol = 'pref_name', string $valueCol = 'pref_value', bool $available = true)
    {
        parent::__construct(
            new ModulesDaoKeyValueCodecAdapter(
                new DaoCallableDbTableStore($query, $fetch, $escape, $tablePrefix, $table, $nameCol, $valueCol, $available)
            )
        );
    }
}
