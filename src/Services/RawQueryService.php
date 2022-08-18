<?php

namespace Litermi\RawQuery\Services;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Litermi\Cache\Models\ModelCacheConst;
use Litermi\Logs\Facades\LogConsoleFacade;

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
    public function query(string $value): bool
    {
        $queryActive = request()->header(ModelCacheConst::HEADER_ACTIVE_RECORD);
        if ($queryActive !== null) {
            $value = Str::replace(array("\r", "\n"), "", $value);
            LogConsoleFacade::full()->log("query complete: ".$value, ['query_active' => $queryActive]);
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
    public function getRow(string $value): array
    {
        $result = DB::connection($this->connection)->select($value);
        return $this->getFirstValues($result);
    }

    /**
     * @param $value
     * @return mixed[]
     */
    private function getFirstValues($value): array
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
