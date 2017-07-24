<?php

namespace Highco\ApiConsumerBundle\DataCollector;

use Highco\ApiConsumerBundle\Log\LogGroup;
use Highco\ApiConsumerBundle\Log\LoggerInterface;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Collecting http data for Symfony profiler
 *
 * Class HttpDataCollector
 * @package Highco\ApiConsumerBundle\DataCollector
 */
class HttpDataCollector extends DataCollector
{

    /**
     * @var \Highco\ApiConsumerBundle\Log\Logger $logger
     */
    protected $logger;

    /**
     * HttpDataCollector constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->data = array(
            'logs' => array(),
            'callCount' => 0,
        );
    }

    /**
     * {@inheritdoc}
     *
     * @param Request         $request
     * @param Response        $response
     * @param \Exception|null $exception
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $messages = $this->logger->getMessages();
        $requestId = $request->getUri();

        // clear log to have only messages related to Symfony request context
        $this->logger->clear();

        $logGroup = $this->getLogGroup($requestId);
        $logGroup->setRequestName($request->getPathInfo());
        $logGroup->addMessages($messages);
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName()
    {
        return 'guzzle';
    }

    /**
     * Returning log entries
     *
     * @return array|mixed $logs
     */
    public function getLogs()
    {
        return array_key_exists('logs', $this->data) ? $this->data['logs'] : array();
    }

    /**
     * Get all messages
     *
     * @return array
     */
    public function getMessages()
    {
        $messages = array();

        foreach ($this->getLogs() as $log) {

            foreach ($log->getMessages() as $message) {

                $messages[] = $message;
            }
        }

        return $messages;
    }

    /**
     * Return amount of http calls
     *
     * @return int
     */
    public function getCallCount()
    {
        return count($this->getMessages());
    }

    /**
     * Returns (new) LogGroup based on given id
     *
     * @param string $id
     *
     * @return LogGroup
     */
    protected function getLogGroup($id)
    {
        if (!isset($this->data['logs'][$id])) {

            $this->data['logs'][$id] = new LogGroup();
        }

        return $this->data['logs'][$id];
    }

    /**
     * Return the color used version
     *
     * @return string
     */
    public final function getIconColor()
    {
        if ((float)$this->getSymfonyVersion() >= 2.8) {
            return $this->data['iconColor'] = '#AAAAAA';
        }
        return $this->data['iconColor'] = '#3F3F3F';
    }

    /**
     * Returns current version symfony
     *
     * @return string
     */
    private function getSymfonyVersion()
    {
        $symfonyVersion = Kernel::VERSION;
        $symfonyVersion = explode('.', $symfonyVersion, -1);
        $symfonyMajorMinorVersion = implode('.', $symfonyVersion);
        return $symfonyMajorMinorVersion;
    }

}
