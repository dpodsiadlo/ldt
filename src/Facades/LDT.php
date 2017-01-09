<?php


namespace DPodsiadlo\LDT\Facades;


use Illuminate\Support\Facades\Facade;

/**
 * Class LDT
 * @package DPodsiadlo\LDT\Facades
 *
 * @method static void send()
 * @method static void toArray()
 * @method static void info($message, $generateTrace = false)
 * @method static void warning($message, $generateTrace = false)
 * @method static void error($message, $generateTrace = true)
 * @method static void exception(\Exception $e)
 * @method static void varDump($object)
 * @method static void response(&$response)
 * @method static void query(\Illuminate\Database\Events\QueryExecuted $query)
 * @method static void request(&$request)
 * @method static void log($request = null, $response = null, $send = false, $storage = null)
 * @method static void store($storage = null)
 *
 *
 */
class LDT extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \DPodsiadlo\LDT\LDT::class;
    }
}