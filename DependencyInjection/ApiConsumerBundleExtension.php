<?php

namespace Highco\ApiConsumerBundle\DependencyInjection;

use Highco\ApiConsumerBundle\Exception\InvalidArgumentException;
use Highco\ApiConsumerBundle\Manager\ManagerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\ExpressionLanguage\Expression;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class ApiConsumerBundleExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor     = new Processor();
        $configuration = new Configuration($this->getAlias(), $container->getParameter('kernel.debug'));
        $config        = $processor->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $this->createLogger($config, $container);

        foreach ($config['clients'] as $name => $options) {

            $argument = [
                'base_uri' => $options['base_url'],
                'handler'  => $this->createHandler($container, $name, $options),
            ];

            // if present, add default options to the constructor argument for the Guzzle client
            if (array_key_exists('options', $options) && is_array($options['options'])) {

                foreach ($options['options'] as $key => $value) {

                    if ($value === null || (is_array($value) && count($value) === 0)) {
                        continue;
                    }

                    if ($key === 'headers') {
                        $argument[$key] = $this->cleanUpHeaders($value);
                        continue;
                    }

                    $argument[$key] = $value;
                }
            }

            $entities = [];

            if (array_key_exists('entities', $options) && is_array($options['entities'])) {
                $entities = $options['entities'];
            }

            $client = new Definition('%api_consumer_bundle.http_client.class%');
            $client->addArgument($argument);

            $managerClass = $options['manager_class'];
            if (!is_subclass_of($managerClass, ManagerInterface::class)) {
                throw new InvalidArgumentException('Provided manager should implements Highco\ApiConsumerBundle\Manager\ManagerInterface');
            }

            $manager = new Definition($managerClass);
            $manager->setArguments([$client, $entities]);

            // set service name based on client name
            $managerServiceName = sprintf('%s.manager.%s', $this->getAlias(), $name);
            $container->setDefinition($managerServiceName, $manager);
            $clientServiceName = sprintf('%s.client.%s', $this->getAlias(), $name);
            $container->setDefinition($clientServiceName, $client);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param                  $name
     * @param array            $config
     *
     * @return Definition
     */
    protected function createHandler(ContainerBuilder $container, $name, array $config)
    {
        $logServiceName = sprintf('guzzle_bundle.middleware.log.%s', $name);
        $log            = $this->createLogMiddleware();
        $container->setDefinition($logServiceName, $log);

        // Event Dispatching service
        $eventServiceName = sprintf('guzzle_bundle.middleware.event_dispatch.%s', $name);
        $eventService     = $this->createEventMiddleware($name);
        $container->setDefinition($eventServiceName, $eventService);

        $logExpression = new Expression(sprintf("service('%s').log()", $logServiceName));
        // Create the event Dispatch Middleware
        $eventExpression = new Expression(sprintf("service('%s').dispatchEvent()", $eventServiceName));

        $handler = new Definition('GuzzleHttp\HandlerStack');
        $handler->setFactory(['GuzzleHttp\HandlerStack', 'create']);

        $handler->addMethodCall('push', [$logExpression]);
        // goes on the end of the stack.
        $handler->addMethodCall('unshift', [$eventExpression]);

        return $handler;
    }

    /**
     * Create Middleware For dispatching events
     *
     * @param  string $name
     *
     * @return Definition
     */
    protected function createEventMiddleware($name)
    {
        $eventMiddleWare = new Definition('%guzzle_bundle.middleware.event_dispatcher.class%');
        $eventMiddleWare->addArgument(new Reference('event_dispatcher'));
        $eventMiddleWare->addArgument($name);

        return $eventMiddleWare;
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     *
     * @return Definition
     * @throws \Symfony\Component\DependencyInjection\Exception\BadMethodCallException
     */
    protected function createLogger(array $config, ContainerBuilder $container)
    {

        if ($config['logging'] === true) {
            $logger = new Definition('%guzzle_bundle.logger.class%');
        } else {
            $logger = new Definition('Highco\ApiConsumerBundle\Log\DevNullLogger');
        }

        $container->setDefinition('guzzle_bundle.logger', $logger);

        return $logger;
    }

    /**
     * Create Middleware for Logging
     *
     * @return Definition
     */
    protected function createLogMiddleware()
    {
        $log = new Definition('%guzzle_bundle.middleware.log.class%');
        $log->addArgument(new Reference('guzzle_bundle.logger'));
        $log->addArgument(new Reference('guzzle_bundle.formatter'));

        return $log;
    }

    /**
     * Clean up HTTP headers
     *
     * @param array $headers
     *
     * @return array
     */
    protected function cleanUpHeaders(array $headers)
    {
        foreach ($headers as $name => $value) {

            // because of standard conventions in YAML dashes are converted to underscores
            // underscores are not allowed in HTTP standard, will be replaced by dash
            $nameClean = str_replace('_', '-', $name);

            unset($headers[$name]);

            $headers[$nameClean] = $value;
        }

        return $headers;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return 'api_consumer';
    }
}
