<?php

use Obelaw\Configs\Classes\ConfigsClass;

if (!function_exists('o_config')) {
    function o_config()
    {
        if (!app()->has('obelaw.o.configs'))
            return app()->instance('obelaw.o.configs', new ConfigsClass);

        return app('obelaw.o.configs');
    }
}
