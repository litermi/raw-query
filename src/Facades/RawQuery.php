<?php

namespace Litermi\RawQuery\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static self to(string $connection)
 * @method static mixed query(string $value)
 * @method static array fetchAll(string $value)
 * @method static array getRow(string $value)
 * @method static mixed getOne(string $value)
 *
 */
class RawQuery extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'raw-query-service';
    }
}
