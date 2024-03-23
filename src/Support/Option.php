<?php

namespace Obelaw\Configuration\Support;

use Illuminate\Support\Facades\Facade;

class Option extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'obelaw.o.configuration';
    }
}
