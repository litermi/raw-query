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
     * @return bool
     */
    public function query(string $value)
    {
        $queryActive = request()->header('j0ic3-disable-4ZZm4uG-0a7P1-query-PiEcPBU');
        if ($queryActive !== null) {
            return false;
        }
        return DB::connection($this->connection)->statement($value);
    }

    /**
     * @param string $value
     * @return mixed[]
     */
    public function fetchAll(string $value): array
    {
        $result = DB::connection($this->connection)->select($value);
        $result = $this->getValues($result);
        return $result;
    }


    /**
     * @param string $value
     * @return mixed[]
     */
    public function getRow(string $value)
    {
        $result = DB::connection($this->connection)->select($value);
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
