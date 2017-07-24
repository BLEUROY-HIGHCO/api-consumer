<?php

namespace Highco\ApiConsumerBundle\Tests\DependencyInjection;

use Highco\ApiConsumerBundle\DependencyInjection\Configuration;
use Highco\ApiConsumerBundle\Manager\Manager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
{
    public function testSingleClientConfigWithOptions()
    {
        $config          = [
            'api_consumer' => [
                'clients' => [
                    'test_client' => [
                        'base_url' => 'http://baseurl/path',
                        'options'  => [
                            'auth'            => [
                                'user',
                                'pass',
                            ],
                            'query'           => [
                            ],
                            'cert'            => 'path/to/cert',
                            'connect_timeout' => 5,
                            'debug'           => false,
                            'decode_content'  => true,
                            'delay'           => 1,
                            'http_errors'     => false,
                            'expect'          => true,
                            'ssl_key'         => 'key',
                            'stream'          => true,
                            'synchronous'     => true,
                            'timeout'         => 30,
                            'verify'          => true,
                            'version'         => '1.1',
                            'headers'         => [],
                        ],
                        'entities' => [],
                    ],
                ],
            ],
        ];
        $processor       = new Processor();
        $processedConfig = $processor->processConfiguration(new Configuration('api_consumer', true), $config);
        $arr             = array_merge($config['api_consumer'], ['logging' => true]);
        $arr['clients']['test_client']['manager_class'] = Manager::class;
        $this->assertEquals($arr, $processedConfig);
    }
}
