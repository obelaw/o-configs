<?php

use Obelaw\Configs\Facades\OConfig;

if (! function_exists('oconfig')) {
    /**
     * Get / set the specified configuration value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  array<string, mixed>|string|null  $key
     * @param  mixed  $default
     * @return ($key is null ? Obelaw\Configs\Services\ConfigurationService : ($key is string ? mixed : null))
     */
    function oconfig($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('obelaw.o.configs');
        }

        if (is_array($key)) {
            foreach ($key as $path => $value) {
                OConfig::set($path, $value);
            }
        }

        return OConfig::get($key, $default);
    }
}
