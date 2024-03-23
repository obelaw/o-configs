# O Configuration

You can add and manage options within the configurations and overwrite the application configurations without having to change the values inside the configuration files.

## Install for laravel

```bash
composer require obelaw/o-configuration
```

### Migrate configurations table

```bash
php artisan migrate
```

## Overwrite app configs

You can overwrite app configs

```php
o_get_option('app.name') // Laravel;

o_set_option('app.name', 'Obelaw');
o_get_option('app.name') // Obelaw;
```

## Usage

You can manage options in a simple way with helpers.

### Add Option

You can add an option through the following line

```php
o_set_option($path, $value);
```

`$path`: The option path that you will use to fetch its value.

`$value`: Put the value of any type of data.

> If this path exists on config files It will be overwritten without modifying the value inside the file.

### Get Option

Fetching value for a specific option

```php
o_get_option($path, $default = null)
```

> If this path does not exist in the configurations table, the value will be fetched from within the file, otherwise, the default value will be displayed if you set.

`$path`: The option path.

`$default`: You can specify a default value if the option is not found.

### Has Option

Make sure the option is there

```php
o_has_option($path)
```

`$path`: The option path.

> Verify that the value exists within the configurations table.

### Remove Option

You can delete any option

```php
o_remove_option($path)
```

`$path`: The option path.

> Delete the option from the configurations table if it exists.

## Use facade

```php
use Obelaw\Configuration\Support\Option;

Option::set($path, $value);
Option::get($path, $default = null);
Option::has($path);
Option::remove($path);
```

## License

The MIT License (MIT). Please see [License File](/LICENSE) for more information.
