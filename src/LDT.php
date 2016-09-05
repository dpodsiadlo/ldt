<?php


namespace DPodsiadlo\LDT;

use App;
use DPodsiadlo\LDT\Log\Entry;
use DPodsiadlo\LDT\Log\Query;
use DPodsiadlo\LDT\Log\Request;
use DPodsiadlo\LDT\Log\Response;
use Exception;


/**
 * Class LDT
 * @package DPodsiadlo
 */
class LDT
{


    private $request = null;
    private $response = null;
    private $entries = [];
    private $startTime = null;
    private $executionTime = 0;
    private $queries = [];


    private $sent = false;

    /**
     * LDT constructor.
     */
    public function __construct()
    {

        $this->startTime = microtime(true);

        set_error_handler(function ($no, $str, $file, $line) {

            switch ($no) {
                case E_ERROR:
                    $this->error("$str at $file:$line");
                    break;
                case E_WARNING:
                    $this->warning("$str at $file:$line");
                    break;
                default:
                    $this->info("$str at $file:$line");

            }

        }, E_ALL);


    }


    public function send()
    {
        $this->sent = true;

        $this->executionTime = microtime(true) - $this->startTime;

        try {

            $fp = fsockopen("127.0.0.1", env('LDT_DEBUG_PORT', 1800));

            if ($fp !== false) {

                $res = (string)$this;

                fwrite($fp, $res, strlen($res));
                fclose($fp);

                return true;
            }

            return false;

        } catch (\Exception $ex) {

        }


        return false;

    }


    public function __destruct()
    {
        if (!$this->sent)
            $this->send();
    }


    public function __toString()
    {
        return (string)json_encode($this->toArray());
    }


    /**
     * @return array
     */
    public function toArray()
    {
        $res = [
            'entries' => $this->entries,
            'queries' => $this->queries,
            'memoryPeakUsage' => memory_get_peak_usage(),
            'executionTime' => $this->executionTime,
            'php' => [
                'sapi' => PHP_SAPI,
                'version' => PHP_VERSION
            ]

        ];

        if (null !== $this->request) {
            $res['request'] = $this->request;
        }
        if (null !== $this->response) {
            $res['response'] = $this->response;
        }

        return $res;
    }


    /**
     * @param Entry $entry
     */
    private function pushEntry(Entry $entry)
    {
        $this->entries[] = $entry;
    }

    /**
     * @param string $message
     * @param bool $generateTrace
     */
    public function info($message, $generateTrace = false)
    {
        $this->pushEntry(Entry::info($message, $generateTrace));
    }

    /**
     * @param string $message
     * @param bool $generateTrace
     */
    public function warning($message, $generateTrace = false)
    {
        $this->pushEntry(Entry::warning($message, $generateTrace));
    }

    /**
     * @param string $message
     * @param bool $generateTrace
     */
    public function error($message, $generateTrace = true)
    {
        $this->pushEntry(Entry::error($message, $generateTrace));
    }


    public function exception(\Exception $e)
    {
        $this->pushEntry(Entry::exception($e));
    }


    /**
     * @param object $object
     */
    public function varDump($object)
    {
        $this->pushEntry(Entry::info(json_encode($object)));
    }

    /**
     * @param mixed $body
     * @param mixed $headers
     */
    public function response(&$response)
    {
        $this->response = new Response($response);
    }


    public function query(\Illuminate\Database\Events\QueryExecuted $query)
    {
        array_unshift($this->queries, new Query($query));
    }


    public function request(&$request)
    {
        $this->request = new Request($request);
    }


    /**
     * @param null $request
     * @param null $response
     * @param bool $send
     * @return LDT
     */
    public static function Log($request = null, $response = null, $send = false)
    {
        $log = new LDT();

        $log->request = $request;
        $log->response = $response;
        
        if ($send)
            $log->send();

        return $log;
    }

}
