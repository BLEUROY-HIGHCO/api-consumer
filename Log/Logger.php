<?php

namespace Highco\ApiConsumerBundle\Log;

use Psr\Log\LoggerTrait;

/**
 * Class Logger
 * @package Highco\ApiConsumerBundle\Log
 */
class Logger implements LoggerInterface
{
    use LoggerTrait;

    /**
     * @var array
     */
    private $messages = [];

    /**
     * Log message
     *
     * @param   string $level
     * @param   string $message
     * @param   array  $context
     *
     * @return  void
     */
    public function log($level, $message, array $context = [])
    {
        $logMessage = new LogMessage($message);
        $logMessage->setLevel($level);

        if ($context) {
            if (!empty($context['request'])) {
                $logMessage->setRequest(new LogRequest($context['request']));
            }

            if (!empty($context['response'])) {
                $logMessage->setResponse(new LogResponse($context['response']));
            }
        }

        $this->messages[] = $logMessage;
    }

    /**
     * Clear messages list
     */
    public function clear()
    {
        $this->messages = [];
    }

    /**
     * Return if messages exist or not
     *
     * @return  boolean
     */
    public function hasMessages()
    {
        return $this->getMessages() ? true : false;
    }

    /**
     * Return log messages
     *
     * @return  array
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
