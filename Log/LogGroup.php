<?php

namespace Highco\ApiConsumerBundle\Log;

/**
 * Class LogGroup
 * @package Highco\ApiConsumerBundle\Log
 */
class LogGroup
{
    /**
     * @var array
     */
    protected $messages = array();

    /**
     * @var string
     */
    protected $requestName;

    /**
     * Set Request Name
     *
     * @param   string $value
     */
    public function setRequestName($value)
    {
        $this->requestName = $value;
    }

    /**
     * Get Request Name
     *
     * @return  string
     */
    public function getRequestName()
    {
        return $this->requestName;
    }

    /**
     * Set Log Messages
     *
     * @param   array $value
     */
    public function setMessages(array $value)
    {
        $this->messages = $value;
    }

    /**
     * Add Log Messages
     *
     * @param   array $value
     */
    public function addMessages(array $value) {

        $this->messages = array_merge($this->messages, $value);
    }

    /**
     * Return Log Messages
     *
     * @return  array
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
