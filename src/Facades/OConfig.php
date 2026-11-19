<?php

namespace Obelaw\Configs\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Values of the current group (config `o-configs.group_resolver`):
 *
 * @method static array all() Get all of the configuration items.
 * @method static boolean has(string $path) Determine if the given configuration value exists.
 * @method static mixed get(string $path, $default = null) Get the specified configuration value.
 * @method static boolean forget(string $path) Forget a configuration value.
 * @method static mixed set(string $path, $value) Set a configuration value.
 *
 * A specific group:
 *
 * @method static \Obelaw\Configs\Services\ConfigurationService group(?string $group) The values of a group (global when null).
 * @method static \Obelaw\Configs\Services\ConfigurationService global() The global values.
 * @method static \Obelaw\Configs\Services\ConfigurationService current() The values of the current group.
 * @method static string|null currentGroup() The group the resolver returns now.
 * @method static void flush() Forget the loaded values.
 *
 * @see \Obelaw\Configs\ConfigsManager
 */
class OConfig extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'obelaw.o.configs';
    }
}
