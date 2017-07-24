<?php

namespace Highco\ApiConsumerBundle\Tests\Events;

use GuzzleHttp\Psr7\Request;
use Highco\ApiConsumerBundle\Events\PreTransactionEvent;
use PHPUnit\Framework\TestCase;

class PreTransactionEventTest extends TestCase
{
    /**
     * Test Instance
     *
     * @covers  \Highco\ApiConsumerBundle\Events\PreTransactionEvent::__construct
     */
    public function testConstruct()
    {
        $serviceName = 'service name';
        /** @var Request $request */
        $request     = $this->getMockBuilder('GuzzleHttp\Psr7\Request')
                            ->setConstructorArgs(array('GET', '/'))
                            ->getMock();
        $preEvent = new PreTransactionEvent($request, $serviceName);
        $this->assertSame($serviceName, $preEvent->getServiceName());
    }
    /**
     * Test Transaction
     *
     * @covers  \Highco\ApiConsumerBundle\Events\PreTransactionEvent::setTransaction
     * @covers  \Highco\ApiConsumerBundle\Events\PreTransactionEvent::getTransaction
     */
    public function testTranscation()
    {
        $method   = 'POST';
        /** @var Request $request */
        $request   = $this->getMockBuilder('GuzzleHttp\Psr7\Request')
                          ->setConstructorArgs(array('GET', '/'))
                          ->getMock();
        $preEvent  = new PreTransactionEvent($request, null);
        $transMock = $this->getMockBuilder('GuzzleHttp\Psr7\Request')
                          ->setConstructorArgs(array($method, '/'))
                          ->getMock();
        $transMock->method('getMethod')->willReturn($method);
        /** @var Request $transMock */
        $preEvent->setTransaction($transMock);
        $transaction = $preEvent->getTransaction();
        $this->assertSame($transaction, $transMock);
        $this->assertSame($method, $transaction->getMethod());
    }
}
