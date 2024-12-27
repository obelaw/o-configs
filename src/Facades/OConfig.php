<?php

namespace Obelaw\Configs\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array all() Get all of the configuration items.
 * @method static boolean has(string $path) Determine if the given configuration value exists.
 * @method static mixed get(string $path, $default = null) Get the specified configuration value.
 * @method static boolean forget(string $path) Forget a configuration value.
 * @method static mixed set(string $path, $value) Set a configuration value.
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
