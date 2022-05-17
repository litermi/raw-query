<?php

namespace Litermi\RawQuery\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static self to(string $text)
 * @method static void message(string $text)
 *
 */
class RawQueryFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'raw-query';
    }
}
