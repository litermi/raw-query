<?php

namespace Litermi\RawQuery\Services;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class RawQueryService
{

    /**
     * @var
     */
    public $connection = "";

    public function __construct()
    {
        $this->connection = config('raw-query.default_connection');
    }

    /**
     * @param string $connection
     * @return $this
     */
    public function to(string $connection): self
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * @param string $value
     * @return Builder
     */
    public function query(string $value): Builder
    {
        return DB::connection('mysql_external')->query($value);
    }

    /**
     * @param string $value
     * @return mixed[]
     */
    public function fetchAll(string $value): array
    {
        $result = DB::connection('mysql_external')->select($value);
        $result = $this->getValues($result);
        return $result;
    }


    /**
     * @param string $value
     * @return mixed[]
     */
    public function getRow(string $value)
    {
        $result = DB::connection('mysql_external')->select($value);
        return $this->getFirstValues($result);
    }

    /**
     * @param $value
     * @return mixed[]
     */
    private function getFirstValues($value)
    {
        $values = collect($value)->first();
        $values = collect($values);
        return $values->toArray();
    }

    /**
     * @param $value
     * @return mixed[]
     */
    private function getValues($value): array
    {
        $values = collect($value);

        $values = $values->map($this->mapChangeObjectToArrayTransformStatic());

        return $values->toArray();
    }

    private function mapChangeObjectToArrayTransformStatic(): callable
    {
        return function ($item, $key) {
            return (array)$item;
        };
    }
}
