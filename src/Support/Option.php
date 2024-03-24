<?php

namespace Obelaw\Configs\Support;

use Illuminate\Support\Facades\Facade;

class Option extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'obelaw.o.configs';
    }
}
