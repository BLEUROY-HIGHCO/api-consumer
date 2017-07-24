<?php

namespace Highco\ApiConsumerBundle\Log;

use Psr\Log\LoggerTrait;

/**
 * Class DevNullLogger
 * @package Highco\ApiConsumerBundle\Log
 */
class DevNullLogger implements LoggerInterface
{
    use LoggerTrait;

    /**
     * @inheritDoc
     */
    public function log($level, $message, array $context = [])
    {
    }

    /**
     * Clear messages list
     *
     * @return void
     */
    public function clear()
    {
    }

    /**
     * Return if messages exist or not
     *
     * @return  boolean
     */
    public function hasMessages()
    {
        return false;
    }

    /**
     * Return log messages
     *
     * @return  array
     */
    public function getMessages()
    {
        return [];
    }
}
