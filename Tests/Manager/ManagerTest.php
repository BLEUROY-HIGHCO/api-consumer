<?php

namespace Highco\ApiConsumerBundle\Tests\Manager;

use GuzzleHttp\Client;
use Highco\ApiConsumerBundle\Exception\InvalidArgumentException;
use Highco\ApiConsumerBundle\Manager\Manager;
use Highco\ApiConsumerBundle\Manager\ManagerInterface;
use Highco\ApiConsumerBundle\Repository\Repository;
use Highco\ApiConsumerBundle\Repository\RepositoryInterface;
use Highco\ApiConsumerBundle\Tests\Entity\Dealer;
use PHPUnit\Framework\TestCase;

class ManagerTest extends TestCase
{
    /**
     * @var ManagerInterface
     */
    private $manager;

    public function setUp()
    {
        $client        = new Client();
        $this->manager = new Manager($client, [
            [
                'class'        => Dealer::class,
                'route_prefix' => '/dealers',
                'repository_class' => Repository::class
            ],
        ]);
    }

    public function testGetRepository()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->manager->getRepository('CoreBundle\\Entity\\User');

        $repository = $this->manager->getRepository(Dealer::class);
        $this->assertInstanceOf(RepositoryInterface::class, get_class($repository));


        $secondRepository = $this->manager->getRepository(Dealer::class);
        $this->assertEquals($repository, $secondRepository);
    }
}
