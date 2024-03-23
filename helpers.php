<?php


//o_set_option($path, $value);
//o_get_option($path, $default = null);
//o_has_option($path);
//o_remove_option($path);

if (!function_exists('o_set_option')) {
    function o_set_option($path, $value)
    {
        return app('obelaw.o.configuration')->set($path, $value);
    }
}

if (!function_exists('o_get_option')) {
    function o_get_option($path, $default = null)
    {
        return app('obelaw.o.configuration')->get($path, $default);
    }
}

if (!function_exists('o_has_option')) {
    function o_has_option($path)
    {
        return app('obelaw.o.configuration')->has($path);
    }
}

if (!function_exists('o_remove_option')) {
    function o_remove_option($path)
    {
        return app('obelaw.o.configuration')->remove($path);
    }
}
