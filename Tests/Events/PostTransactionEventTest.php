<?php

namespace Highco\ApiConsumerBundle\Tests\Events;

use GuzzleHttp\Psr7\Response;
use Highco\ApiConsumerBundle\Events\PostTransactionEvent;
use PHPUnit\Framework\TestCase;

class PostTransactionEventTest extends TestCase
{
    /**
     * Test Instance
     *
     * @covers  \Highco\ApiConsumerBundle\Events\PostTransactionEvent::__construct
     */
    public function testConstruct()
    {
        $serviceName = 'service name';
        $response    = $this->createMock('GuzzleHttp\Psr7\Response');
        /** @var Response $response */
        $postEvent   = new PostTransactionEvent($response, $serviceName);
        $this->assertSame($serviceName, $postEvent->getServiceName());
    }
    /**
     * Test Transaction
     *
     * @covers  \Highco\ApiConsumerBundle\Events\PostTransactionEvent::setTransaction
     * @covers  \Highco\ApiConsumerBundle\Events\PostTransactionEvent::getTransaction
     */
    public function testTranscation()
    {
        $statusCode = 204;
        /** @var Response $response */
        $response  = $this->createMock('GuzzleHttp\Psr7\Response');
        $postEvent = new PostTransactionEvent($response, null);
        $transMock = $this->getMockBuilder('GuzzleHttp\Psr7\Response')
                          ->getMock();
        $transMock->method('getStatusCode')->willReturn($statusCode);
        /** @var Response $transMock */
        $postEvent->setTransaction($transMock);
        $transaction = $postEvent->getTransaction();
        $this->assertSame($transaction, $transMock);
        $this->assertSame($statusCode, $transaction->getStatusCode());
    }

}
