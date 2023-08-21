<?php

namespace Litermi\RawQuery\Services;

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
     * @return mixed
     */
    public function query(string $value): mixed
    {
        if(Str::contains(Str::lower($value),'select')   && Str::contains(Str::lower($value),'from')){
           return $this->fetchAll($value);
        }

        $value = $this->replaceJumpLineAndEmptyLine($value);
        $queryActive = request()->header(ModelCacheConst::HEADER_ACTIVE_RECORD);
        if ($queryActive !== null) {
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
        $value = $this->replaceJumpLineAndEmptyLine($value);
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
        $value = $this->replaceJumpLineAndEmptyLine($value);
        $result = DB::connection($this->connection)->select($value);
        return $this->getFirstValues($result);
    }

    public function getOne(string $value)
    {
        $value = $this->replaceJumpLineAndEmptyLine($value);
        $result = DB::connection($this->connection)->select($value);
        $valuesResult = $this->getFirstValues($result);
        if(is_array($valuesResult) === false){
            return null;
        }
        $valuesResult = collect($valuesResult);
        return $valuesResult->count() !== 0 ? $valuesResult->first() : null;
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

    /**
     * @param $value
     * @return array|string|string[]|null
     */
    private function replaceJumpLineAndEmptyLine($value){
        $value = Str::replace(array("\r", "\n"), "", $value);
        return preg_replace('!\s+!', ' ', $value);
    }
}
