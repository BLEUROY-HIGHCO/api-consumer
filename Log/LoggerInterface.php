<?php

namespace Highco\ApiConsumerBundle\Log;

use Psr\Log;

/**
 * Interface LoggerInterface
 * @package Highco\ApiConsumerBundle\Log
 */
interface LoggerInterface extends Log\LoggerInterface
{

    /**
     * Clear messages list
     *
     * @return void
     */
    public function clear();

    /**
     * Return if messages exist or not
     *
     * @return  boolean
     */
    public function hasMessages();

    /**
     * Return log messages
     *
     * @return  array
     */
    public function getMessages();
}
