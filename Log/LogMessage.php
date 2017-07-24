<?php

namespace Highco\ApiConsumerBundle\Log;

/**
 * Class LogMessage
 * @package Highco\ApiConsumerBundle\Log
 */
class LogMessage
{
    /**
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $level;

    /**
     * @var LogRequest
     */
    protected $request;

    /**
     * @var LogResponse
     */
    protected $response;

    /**
     * Constructor
     *
     * @param   string $message
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Set log level
     *
     * @param  string $value
     */
    public function setLevel($value)
    {
        $this->level = $value;
    }

    /**
     * Returning log level
     *
     * @return  string
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Returning log message
     *
     * @return  string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set Log Request
     *
     * @param   LogRequest $value
     */
    public function setRequest(LogRequest $value)
    {
        $this->request = $value;
    }

    /**
     * Get Log Request
     *
     * @return  LogRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set Log Response
     *
     * @param   LogResponse $value
     */
    public function setResponse(LogResponse $value)
    {
        $this->response = $value;
    }

    /**
     * Get Log Response
     *
     * @return  LogResponse
     */
    public function getResponse()
    {
        return $this->response;
    }
}
