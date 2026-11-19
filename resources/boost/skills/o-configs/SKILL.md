---
name: o-configs
description: >-
  Read, write and scope runtime configuration stored in the database with obelaw/o-configs
  (oconfig() helper, OConfig facade, HasConfigs trait, group resolvers). Use when code reads or
  saves settings with oconfig()/OConfig, gives a model (tenant, store, user, branch) its own
  settings, makes configuration per tenant, overrides Laravel config values at runtime,
  builds a settings page, writes tests around stored settings, or upgrades o-configs from 1.x.
---

# o-configs

`obelaw/o-configs` stores configuration values in the database and reads them with the same
dot-notation paths as Laravel's `config()`. A value that is not stored falls back to
`config()`, so `oconfig('mail.from.address')` works before anything has been saved.

## Mental model

All values live in one table, `o_configs`:

| column  | meaning                                                                 |
|---------|-------------------------------------------------------------------------|
| `group` | whose value it is: `''` for global, or a key such as `App\Models\Tenant:5` |
| `path`  | dot-notation path, e.g. `shop.currency` (unique per group)              |
| `value` | JSON-encoded value (scalar, array, null)                                |

**Read order** for `get($path, $default)` on a group:

1. the group's own stored value
2. the global group's stored value (only when `o-configs.fallback_to_global` is `true`, the default)
3. Laravel `config($path, $default)`

**Writes** (`set`, `forget`) only touch the group they are called on and never reach the
global group or Laravel's config repository.

**The current group** is the group `oconfig()` and `OConfig::get/set/has/forget/all` use. The
class in `o-configs.group_resolver` decides it. The default `GlobalGroupResolver` returns
`null`, which means the global group.

## API

### Current group: `oconfig()` / `OConfig`

```php
use Obelaw\Configs\Facades\OConfig;

oconfig('shop.currency');                 // get, falls back as above
oconfig('shop.currency', 'USD');          // get with default
oconfig(['shop.currency' => 'EGP',        // set many, returns null
         'shop.tax_rate' => 0.14]);
oconfig();                                // the ConfigsManager

OConfig::get('shop.currency', 'USD');
OConfig::set('shop.currency', 'EGP');     // returns the value
OConfig::has('shop.currency');            // stored in THIS group only (ignores fallbacks)
OConfig::forget('shop.currency');         // deletes from THIS group, returns bool
OConfig::all();                           // this group's stored values as a nested array
```

### A specific group

```php
OConfig::global()->get('shop.currency');                 // global values
OConfig::group('App\Models\Tenant:5')->set('a.b', 1);    // any group key
OConfig::group(null);                                    // same as global()
OConfig::current();                                      // ConfigurationService of the current group
OConfig::currentGroup();                                 // ?string from the resolver
OConfig::query('App\Models\Tenant:5');                   // Eloquent Builder on that group's rows
OConfig::flush();                                        // drop loaded values; re-read on next call
```

`group()`, `global()` and `current()` return `Obelaw\Configs\Services\ConfigurationService`, which
has the same `get / set / has / forget / all` methods plus `group()` and `query()`.

### Per-model values: `HasConfigs`

```php
use Obelaw\Configs\Traits\HasConfigs;

class Store extends Model
{
    use HasConfigs;
}

$store->configGroup();                       // "App\Models\Store:1" (morph class + ':' + key)
$store->configs()->set('invoice.prefix', 'ST01-');
$store->configs()->get('invoice.prefix');    // store → global → config() → default
$store->configs()->has('invoice.prefix');
$store->configs()->forget('invoice.prefix');
$store->configs()->all();
$store->allConfigs()->delete();              // Builder on the store's rows
```

A model's values sit in `o_configs` under the group `configGroup()` returns. There is no
relation and no second table.

## Configuration

Publish with `php artisan vendor:publish --tag=o-configs-config` → `config/o-configs.php`:

```php
'group_resolver'     => Obelaw\Configs\Resolvers\GlobalGroupResolver::class, // container-built
'fallback_to_global' => true,                       // group miss → global before config()
'connection'         => env('OCONFIGS_CONNECTION'), // null = the model's (Twist) connection
```

Migrations load automatically. Run `php artisan migrate`.

## Recipes

### Per-tenant configuration for every package

Implement `Obelaw\Configs\Contracts\GroupResolver` and register it. Each package that calls
`oconfig()` then reads and writes the tenant's values without knowing about tenancy.

```php
namespace App\Configs;

use App\Models\Tenant;
use Obelaw\Configs\Contracts\GroupResolver;

final class TenantGroupResolver implements GroupResolver
{
    public function current(): ?string
    {
        return Tenant::current()?->configGroup(); // null (global) outside a tenant
    }
}
```

```php
// config/o-configs.php
'group_resolver' => App\Configs\TenantGroupResolver::class,
'connection'     => 'central', // database-per-tenant: keep o_configs central
```

The manager calls the resolver on every call, so the group follows the tenant as it changes
during a request. A resolver must be cheap and must not call `oconfig()` itself, because that
would recurse.

### Reading a model's setting with a sensible chain

```php
public function invoicePrefix(Store $store): string
{
    return $store->configs()->get('billing.invoice_prefix', 'INV-');
}
```

Do not pass `config('...')` as the default. The fallback chain already consults `config()`.

### Pushing stored values into Laravel's config

`oconfig()` does not change `config()`. Framework code (mail, services) only sees stored values
if you copy them over:

```php
// AppServiceProvider::boot()
use Obelaw\Configs\Models\Config;

// The table is missing during the first migrate; getTable() includes any Twist prefix.
if (Schema::hasTable((new Config)->getTable())) {
    config([
        'mail.default'          => oconfig('mail.default'),
        'mail.from.address'     => oconfig('mail.from.address'),
        'mail.mailers.smtp.host'=> oconfig('mail.mailers.smtp.host'),
    ]);
}
```

In `boot()` the tenant is usually not known yet, so this reads the **global** group. For
per-tenant overrides, copy the values after the tenant is identified, for example in middleware
or a tenancy "tenant initialized" listener.

### Feature flags

```php
$enabled = (bool) oconfig("features.{$name}", false);
OConfig::set("features.{$name}", true);
```

### Settings page fields (Filament)

Register fields from a service provider's `boot()`. `ConfigPlugin` adds a page that saves them
with `oconfig([...])`, that is, to the current group:

```php
use Obelaw\Configs\Schema\Section;
use Obelaw\Configs\Schema\Fields\Text;

Section::make(
    name: 'Shop',
    description: 'Store front settings',
    schema: fn () => [
        Text::make('currency', 'shop.currency', 'Currency', 'e.g. EGP'),
    ],
);
```

## Pitfalls

- **Forget the exact path you set.** If you `set('mail', [...])` and then `forget('mail.host')`,
  the key is removed in memory but no row is deleted (`false` is returned), and it is back on
  the next request. Store either a parent array or its leaves, never both.
- **`has()` is not `get() !== null`.** It only checks the group's own stored values and ignores
  the global group and `config()`.
- **Unsaved models** have no key, so their group is `"App\Models\Store:"`. Call `configs()`
  only on persisted models.
- **Deleting a model does not delete its values.** Clean up in the model if needed:
  `static::deleted(fn ($m) => $m->allConfigs()->delete());`
- **Morph map changes orphan values.** The group uses `getMorphClass()`. Set up
  `Relation::enforceMorphMap()` before values are stored, or migrate the `group` column when
  you add one later.
- **Lifetime.** The manager is a scoped binding, so each request, queued job and Octane request
  starts fresh. Within one long job or command, call `OConfig::flush()` to see values written
  by other processes.
- **Loading.** The first access to a group loads all of that group's rows in one query, and
  later reads come from memory. Keep groups small and avoid large blobs.
- **Paths.** Use lowercase dot-notation that matches Laravel config keys. Dots always nest, so
  a key itself cannot contain `.`.

## Testing

```php
use Obelaw\Configs\Facades\OConfig;

uses(RefreshDatabase::class);

it('falls back from group to global to config', function () {
    config(['shop.currency' => 'USD']);
    expect(oconfig('shop.currency'))->toBe('USD');

    OConfig::global()->set('shop.currency', 'EGP');
    $store = Store::factory()->create();

    expect($store->configs()->get('shop.currency'))->toBe('EGP');

    $store->configs()->set('shop.currency', 'SAR');
    expect($store->configs()->get('shop.currency'))->toBe('SAR')
        ->and(OConfig::global()->get('shop.currency'))->toBe('EGP');
});

it('uses the resolver for the current group', function () {
    $tenant = Tenant::factory()->create();
    config(['o-configs.group_resolver' => FixedResolver::class]);
    app()->instance(FixedResolver::class, new FixedResolver($tenant->configGroup()));

    oconfig(['shop.currency' => 'EGP']);

    expect($tenant->configs()->get('shop.currency'))->toBe('EGP')
        ->and(OConfig::global()->has('shop.currency'))->toBeFalse();
});
```

Here `FixedResolver` is a small test class that implements `GroupResolver` and returns the
group given to its constructor. If a test writes rows directly with `Config::create()`, call
`OConfig::flush()` before reading.

## Upgrading from 1.x

- `php artisan migrate` moves global rows to group `''` and each `o_config_models` row to group
  `{modelable_type}:{modelable_id}`, then drops `o_config_models`. `migrate:rollback` restores
  the 1.x tables.
- Removed: `Obelaw\Configs\Models\ConfigModel` and `Obelaw\Configs\Services\ConfigurationModelService`.
  Replace `ConfigModel` queries with `Config::where('group', $model->configGroup())` or
  `$model->allConfigs()`.
- `$model->configs()` returns `ConfigurationService`, and `allConfigs()` returns a `Builder`
  (previously a `morphMany`). Change `->allConfigs()->get()` relation usage accordingly.
- `oconfig()` with no argument returns `ConfigsManager`.
