<?php

namespace Ksfraser\Prefs\Sync;

use Ksfraser\Prefs\Contracts\PrefsStoreInterface;
use Ksfraser\Prefs\Schema\PrefsSchema;
use RuntimeException;

class PrefsDiffEngine
{
    /**
     * @param string[]|null $keys Optional explicit key list (recommended when a store can't enumerate all()).
     * @return array{
     *   onlyInFrom: array<string,mixed>,
     *   onlyInTo: array<string,mixed>,
     *   different: array<string,array{from:mixed,to:mixed}>,
     *   same: array<string,mixed>,
     *   missingRequiredInFrom: string[],
     *   missingRequiredInTo: string[]
     * }
     */
    public function diff(
        PrefsStoreInterface $from,
        PrefsStoreInterface $to,
        ?array $keys = null,
        ?string $prefix = null,
        ?PrefsSchema $schema = null
    ): array {
        if (!$from->isAvailable()) {
            throw new RuntimeException('Source store is not available');
        }
        if (!$to->isAvailable()) {
            throw new RuntimeException('Target store is not available');
        }

        $fromAll = $from->all($prefix);
        $toAll = $to->all($prefix);

        $keySet = [];
        foreach (array_keys($fromAll) as $k) {
            $keySet[(string)$k] = true;
        }
        foreach (array_keys($toAll) as $k) {
            $keySet[(string)$k] = true;
        }

        if ($schema !== null) {
            foreach ($schema->keys() as $k) {
                $keySet[(string)$k] = true;
            }
        }

        if ($keys !== null) {
            foreach ($keys as $k) {
                $k = (string)$k;
                if ($k !== '') {
                    $keySet[$k] = true;
                }
            }
        }

        $allKeys = array_keys($keySet);
        sort($allKeys);

        $onlyInFrom = [];
        $onlyInTo = [];
        $different = [];
        $same = [];

        foreach ($allKeys as $k) {
            $hasFrom = $from->has($k);
            $hasTo = $to->has($k);

            if ($hasFrom && !$hasTo) {
                $onlyInFrom[$k] = $from->get($k, null);
                continue;
            }
            if (!$hasFrom && $hasTo) {
                $onlyInTo[$k] = $to->get($k, null);
                continue;
            }
            if (!$hasFrom && !$hasTo) {
                continue;
            }

            $vFrom = $from->get($k, null);
            $vTo = $to->get($k, null);

            if ($vFrom === $vTo) {
                $same[$k] = $vFrom;
            } else {
                $different[$k] = ['from' => $vFrom, 'to' => $vTo];
            }
        }

        $missingRequiredInFrom = [];
        $missingRequiredInTo = [];
        if ($schema !== null) {
            foreach ($schema->required() as $def) {
                $k = $def->getKey();
                if (!$from->has($k)) {
                    $missingRequiredInFrom[] = $k;
                }
                if (!$to->has($k)) {
                    $missingRequiredInTo[] = $k;
                }
            }
        }

        return [
            'onlyInFrom' => $onlyInFrom,
            'onlyInTo' => $onlyInTo,
            'different' => $different,
            'same' => $same,
            'missingRequiredInFrom' => $missingRequiredInFrom,
            'missingRequiredInTo' => $missingRequiredInTo,
        ];
    }
}
