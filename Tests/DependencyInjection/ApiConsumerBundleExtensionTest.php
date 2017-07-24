<?php

namespace Highco\ApiConsumerBundle\Tests\DependencyInjection;

use GuzzleHttp\Psr7\Uri;
use Highco\ApiConsumerBundle\DependencyInjection\ApiConsumerBundleExtension;
use Highco\ApiConsumerBundle\Exception\InvalidArgumentException;
use Highco\ApiConsumerBundle\Exception\InvalidResourceException;
use Highco\ApiConsumerBundle\Manager\Manager;
use Highco\ApiConsumerBundle\Manager\ManagerInterface;
use Highco\ApiConsumerBundle\Tests\DependencyInjection\Fixtures\FakeClient;
use Highco\ApiConsumerBundle\Tests\DependencyInjection\Fixtures\FakeEntity;
use Highco\ApiConsumerBundle\Tests\DependencyInjection\Fixtures\FakeManager;
use Highco\ApiConsumerBundle\Tests\DependencyInjection\Fixtures\FakeRepository;
use Highco\ApiConsumerBundle\Tests\Manager\ManagerTest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ApiConsumerBundleExtensionTest extends TestCase
{
    public function testApiConsumerBundleExtension()
    {
        $container = $this->createContainer();
        $extension = new ApiConsumerBundleExtension();
        $extension->load($this->getConfigs(), $container);
        // test Client
        $this->assertTrue($container->hasDefinition('api_consumer.manager.test_api'));
        $testManager = $container->get('api_consumer.manager.test_api');
        $this->assertInstanceOf(ManagerInterface::class, $testManager);

        $testApi = $container->get('api_consumer.client.test_api');
        $this->assertInstanceOf('GuzzleHttp\Client', $testApi);
        $this->assertEquals(new Uri('//api.domain.tld/path'), $testApi->getConfig('base_uri'));

        // test Services
        $this->assertTrue($container->hasDefinition('guzzle_bundle.middleware.log.test_api'));
        $this->assertTrue($container->hasDefinition('guzzle_bundle.middleware.event_dispatch.test_api'));
    }

    /**
     * @return ContainerBuilder
     */
    private function createContainer()
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.debug', true);
        $container->set('event_dispatcher', $this->createMock('Symfony\Component\EventDispatcher\EventDispatcherInterface'));

        return $container;
    }

    public function testOverwriteManager()
    {
        $container = $this->createContainer();
        $extension = new ApiConsumerBundleExtension();
        $this->expectException(InvalidArgumentException::class);
        $extension->load($this->getConfigs(FakeManager::class), $container);
    }

    public function testOverwriteRepository()
    {
        $container = $this->createContainer();
        $extension = new ApiConsumerBundleExtension();
        $extension->load($this->getConfigs(Manager::class), $container);
        /** @var ManagerInterface $testManager */
        $testManager = $container->get('api_consumer.manager.test_api');

        $this->expectException(InvalidResourceException::class);
        $testManager->getRepository(FakeEntity::class);
    }

    /**
     * @param $managerClass
     *
     * @return array
     */
    private function getConfigs($managerClass = null)
    {
        $configs = [
            [
                'clients' => [
                    'test_api' => [
                        'base_url' => '//api.domain.tld/path',
                        'entities' => [
                            [
                                'class'            => FakeEntity::class,
                                'route_prefix'     => '/fakeEntities',
                                'repository_class' => FakeRepository::class,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        if (null !== $managerClass) {
            $configs[0]['clients']['test_api']['manager_class'] = $managerClass;
        }

        return $configs;
    }
}
