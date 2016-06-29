<?php


namespace DPodsiadlo\LDT\Log;

use JsonSerializable;


/**
 * Class Query
 * @package DPodsiadlo\Log
 */
class Query implements JsonSerializable
{

    private $sql = "";
    private $bindings = [];
    private $time = 0;


    /**
     * Query constructor.
     * @param \Illuminate\Database\Events\QueryExecuted $query
     */
    public function __construct(\Illuminate\Database\Events\QueryExecuted $query)
    {
        $this->sql = $query->sql;

        foreach ($query->bindings as $binding)
            $this->bindings[] = is_object($binding) ? (string)$binding : $binding;

        $this->time = $query->time;
    }


    /**
     * @return array
     */
    public function toArray()
    {
        return [
            "sql" => $this->sql,
            "bindings" => $this->bindings,
            "time" => $this->time
        ];
    }


    public function jsonSerialize()
    {
        return $this->toArray();
    }


}