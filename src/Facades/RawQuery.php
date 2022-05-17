<?php

namespace Litermi\RawQuery\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static self to(string $connection)
 * @method static void query(string $value)
 * @method static void fetchAll(string $value)
 * @method static void getRow(string $value)
 *
 */
class RawQuery extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'raw-query-service';
    }
}
