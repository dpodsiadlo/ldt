<?php

namespace DPodsiadlo\Handlers;

use DPodsiadlo\Facades\LDT;
use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;

class LDTHandler extends AbstractProcessingHandler
{

    public function __construct($level = Logger::DEBUG, $bubble = true)
    {

        parent::__construct($level, $bubble);
    }

    protected function write(array $record)
    {

        switch ($record['level']) {
            case Logger::DEBUG:
            case Logger::INFO:
                LDT::info($record['message']);
                break;
            case Logger::NOTICE:
            case Logger::WARNING:
                LDT::warning($record['message']);
                break;
            case Logger::ERROR:
            case Logger::CRITICAL:
            case Logger::ALERT:
            case Logger::EMERGENCY:
                LDT::error($record['message']);
                break;


        }
    }

}