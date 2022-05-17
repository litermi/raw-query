<?php

namespace Litermi\RawQuery\Facades;

use Illuminate\Support\Facades\Facade;

/**
 *
 */
class RawQueryFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'raw-query';
    }
}
