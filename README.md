# ksfraser/prefs

A small PHP library that abstracts storing/retrieving preferences across multiple platforms.

## Goals

- A **single API** for module code to `get()` / `set()` preferences
- Multiple backends (stores):
  - Native app DB table (PDO)
  - WordPress options/settings
  - SuiteCRM Administration settings
  - FrontAccounting system preferences
  - FrontAccounting (or any app) DB table
- Easy **fallback order** (e.g. WordPress → DB table)

## Quick start

```php
use Ksfraser\Prefs\Prefs;
use Ksfraser\Prefs\Stores\CompositeStore;
use Ksfraser\Prefs\Stores\PrefixedStore;
use Ksfraser\Prefs\Stores\WordPressOptionsStore;
use Ksfraser\Prefs\Stores\PdoTableStore;

$store = new CompositeStore([
    new WordPressOptionsStore('ksf_'),
    new PdoTableStore($pdo, 'app_prefs')
]);

$prefs = new Prefs(new PrefixedStore($store, 'gencat.'));

$enabled = $prefs->getBool('output.enabled', true);
$prefs->set('output.enabled', false);
```

## Stores

- `PdoTableStore`: generic DB table via PDO
- `FrontAccountingDbTableStore`: uses FrontAccounting `db_*` functions + `TB_PREF`
- `WordPressOptionsStore`: uses `get_option`/`update_option`/`delete_option`
- `SuiteCrmAdministrationStore`: uses SuiteCRM `Administration` settings
- `FrontAccountingSysPrefsStore`: best-effort FrontAccounting system prefs wrapper
- `CompositeStore`: try multiple stores in order, with fallback
- `PrefixedStore`: adds a prefix/namespace to all keys

## Value encoding

Values are stored as strings. Arrays/objects are stored as JSON with a prefix marker so types round-trip.

## Schema (required keys + defaults)

Apps/modules can declare which prefs **must exist** and provide **default values**.

```php
use Ksfraser\Prefs\Schema\GlobalPrefsSchemaRegistry;
use Ksfraser\Prefs\Schema\PrefsSchema;

$schema = (new PrefsSchema())
  ->addKey('gencat.output.enabled', true, true, 'Enable output generation')
  ->addKey('gencat.square.location_id', '', true, 'Square location id');

GlobalPrefsSchemaRegistry::addSchema($schema);
```

When a schema is provided to `PrefsStoreManager`, you can call `applyDefaultsToCurrent()` / `applyDefaultsToTarget()`.

## Diff / review

Use `Ksfraser\Prefs\Sync\PrefsDiffEngine` to compute differences between two stores, including:

- keys only in one store
- keys that exist in both but have different values
- missing required keys (when a schema is supplied)

## Status

Initial scaffold. Adapters use runtime checks so they can be included safely even when a platform is not loaded.
