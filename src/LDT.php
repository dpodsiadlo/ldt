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

    const LOG_STORAGE_DAILY = "daily";
    const LOG_STORAGE_SINGLE = "single";

    /**
     * @var Request $request
     */
    private $request = null;
    /**
     * @var Response $response
     */
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

            $socket = fsockopen("127.0.0.1", env('LDT_DEBUG_PORT', 1800));

            if ($socket !== false) {

                $res = (string)$this;

                fwrite($socket, $res, strlen($res));
                fclose($socket);

                return true;
            }

            return false;

        } catch (\Exception $ex) {

        }


        return false;

    }

    public function store($storage)
    {
        switch (config('app.log', 'single')) {
            case self::LOG_STORAGE_DAILY:
                $logFileName = $storage . "-" . date("Y-m-d") . ".log";
                break;
            default:
                $logFileName = $storage . ".log";

        }

        $logFile = fopen(storage_path("logs/" . $logFileName), "a");

        if ($logFile) {

            fputs($logFile, "[" . date("Y-m-d h:i:s") . "] " . $this->getName() . "\n");

            fputs($logFile, $this->toString(true) . "\n");

            fclose($logFile);
        }
    }


    public function __destruct()
    {
        if (!$this->sent)
            $this->send();
    }


    public function __toString()
    {
        return $this->toString();
    }

    public function toString($prettyPrint = false)
    {
        return (string)json_encode($this->toArray(), $prettyPrint ? JSON_PRETTY_PRINT : 0);
    }

    public function getName()
    {
        return !empty($this->request) ? $this->request->getName() : "cli";
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
            $res['request'] = $this->request->toArray();
        }
        if (null !== $this->response) {
            $res['response'] = $this->response->toArray();
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
     * @param null $storage
     * @return LDT
     */
    public function log($request = null, $response = null, $send = false, $storage = null)
    {
        $log = new LDT();

        $log->request = $request;
        $log->response = $response;

        if ($send)
            $log->send();


        if (!empty($storage)) {
            $log->store($storage);

        }


        return $log;
    }

}
