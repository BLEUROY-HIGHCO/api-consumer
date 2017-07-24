<?php

namespace Highco\ApiConsumerBundle\Tests\DataCollector;

use Highco\ApiConsumerBundle\DataCollector\HttpDataCollector;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HttpDataCollectorTest extends TestCase
{
    /**
     * @var \Highco\ApiConsumerBundle\Log\Logger
     */
    protected $logger;

    /**
     * SetUp: before executing each test function
     */
    public function setUp()
    {
        $this->logger = $this->getMockBuilder('Highco\ApiConsumerBundle\Log\Logger')
                             ->getMock();
    }

    /**
     * Test Constructor
     *
     * @covers  \Highco\ApiConsumerBundle\DataCollector\HttpDataCollector::__construct
     */
    public function testConstruct()
    {
        $collector = new HttpDataCollector($this->logger);
        $data      = unserialize($collector->serialize());
        $expected  = [
            'logs'      => [],
            'callCount' => 0,
        ];
        $this->assertSame($expected, $data);
    }

    /**
     * Test Collecting Data
     *
     * @covers  \Highco\ApiConsumerBundle\DataCollector\HttpDataCollector::collect
     * @covers  \Highco\ApiConsumerBundle\DataCollector\HttpDataCollector::getLogs
     * @covers  \Highco\ApiConsumerBundle\DataCollector\HttpDataCollector::getLogGroup
     */
    public function testCollect()
    {
        $this->logger->expects($this->once())
                     ->method('getMessages')
                     ->willReturn(['test message']);
        $collector = new HttpDataCollector($this->logger);
        /** @var Response $response */
        $response = $this->getMockBuilder('Symfony\Component\HttpFoundation\Response')
                         ->getMock();
        /** @var Request $request */
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
                        ->getMock();
        $request->expects($this->once())
                ->method('getPathInfo')
                ->willReturn('id');
        $collector->collect($request, $response);
        $logs = $collector->getLogs();
        /** @var \Highco\ApiConsumerBundle\Log\LogGroup $log */
        foreach ($logs as $log) {
            $this->assertInstanceOf('Highco\ApiConsumerBundle\Log\LogGroup', $log);
            $this->assertSame(['test message'], $log->getMessages());
            $this->assertSame('id', $log->getRequestName());
        }
    }

    /**
     * Test Collector Name
     *
     * @covers  \Highco\ApiConsumerBundle\DataCollector\HttpDataCollector::getName
     */
    public function testName()
    {
        $collector = new HttpDataCollector($this->logger);
        $this->assertSame('guzzle', $collector->getName());
    }

    /**
     * Test Log Messages
     *
     * @covers  \Highco\ApiConsumerBundle\DataCollector\HttpDataCollector::getMessages
     */
    public function testMessages()
    {
        $this->logger->expects($this->once())
                     ->method('getMessages')
                     ->willReturn(['test message #1', 'test message #2']);
        $collector = new HttpDataCollector($this->logger);
        /** @var Response $response */
        $response = $this->getMockBuilder('Symfony\Component\HttpFoundation\Response')
                         ->getMock();
        /** @var Request $request */
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
                        ->getMock();
        $request->expects($this->once())
                ->method('getPathInfo')
                ->willReturn('id');
        $collector->collect($request, $response);
        $messages = $collector->getMessages();
        /** @var \Highco\ApiConsumerBundle\Log\LogMessage $message */
        foreach ($messages as $i => $message) {
            $text = sprintf('test message #%d', ($i + 1));
            $this->assertSame($text, $message);
        }
    }

    /**
     * Test Call Count
     *
     * @covers  \Highco\ApiConsumerBundle\DataCollector\HttpDataCollector::getCallCount
     */
    public function testCallCount()
    {
        $this->logger->expects($this->once())
                     ->method('getMessages')
                     ->willReturn(['test message #1', 'test message #2']);
        $collector = new HttpDataCollector($this->logger);
        $response  = $this->getMockBuilder('Symfony\Component\HttpFoundation\Response')
                          ->getMock();
        $request   = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
                          ->getMock();
        $request->expects($this->once())
                ->method('getPathInfo')
                ->willReturn('id');
        $collector->collect($request, $response);
        $this->assertSame(2, $collector->getCallCount());
    }
}
