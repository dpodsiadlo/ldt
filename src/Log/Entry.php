<?php


namespace DPodsiadlo\LDT\Log;

use JsonSerializable;


/**
 * Class Entry
 * @package DPodsiadlo\Log
 */
class Entry implements JsonSerializable
{

    const Warning = "Warning";
    const Info = "Info";
    const Error = "Error";

    private $type = "";
    private $message = "";
    private $trace = [];


    /**
     * Entry constructor.
     * @param string $type Type of entry
     * @param string $message Message
     * @param bool $generateTrace Whether generate backtrace information
     */
    public function __construct($type, $message, $generateTrace = false)
    {
        $this->type = $type;
        $this->message = $message;

        if (true === $generateTrace) {
            $this->trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        }
    }


    /**
     * @param string $message
     * @param bool $generateTrace
     * @return Entry
     */
    public static function info($message, $generateTrace = false)
    {
        return new Entry(self::Info, $message, $generateTrace);
    }

    /**
     * @param string $message
     * @param bool $generateTrace
     * @return Entry
     */
    public static function warning($message, $generateTrace = false)
    {
        return new Entry(self::Warning, $message, $generateTrace);
    }

    /**
     * @param string $message
     * @param bool $generateTrace
     * @return Entry
     */
    public static function error($message, $generateTrace = true)
    {
        return new Entry(self::Error, $message, $generateTrace);
    }

    public static function exception(\Exception $e)
    {
        $entry = new Entry(self::Error, $e->getMessage());
        $entry->trace = $e->getTrace();

        return $entry;
    }


    /**
     * @return array
     */
    public function toArray()
    {
        return [
            "type" => $this->type,
            "message" => $this->message,
            "trace" => $this->trace
        ];
    }


    public function jsonSerialize()
    {
        return $this->toArray();
    }


}