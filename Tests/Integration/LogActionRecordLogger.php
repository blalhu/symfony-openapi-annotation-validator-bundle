<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Integration;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

class LogActionRecordLogger extends AbstractLogger
{
    protected $logEntries = [
        LogLevel::EMERGENCY => [],
        LogLevel::ALERT => [],
        LogLevel::CRITICAL => [],
        LogLevel::ERROR => [],
        LogLevel::WARNING => [],
        LogLevel::NOTICE => [],
        LogLevel::INFO => [],
        LogLevel::DEBUG => [],
    ];

    /**
     * @inheritDoc
     */
    public function emergency($message, array $context = array())
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function alert($message, array $context = array())
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function critical($message, array $context = array())
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function error($message, array $context = array())
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function warning($message, array $context = array())
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function notice($message, array $context = array())
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function info($message, array $context = array())
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function debug($message, array $context = array())
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function log($level, $message, array $context = array())
    {
        $this->logEntries[$level][] = [
            'message' => $message,
            'context' => $context,
        ];
    }

    public function getLogEntries(): array
    {
        return $this->logEntries;
    }

    public function getLogEntriesByLevel($level): array
    {
        return $this->getLogEntries()[$level];
    }
}
