<?php

namespace Highco\ApiConsumerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @var string $alias
     */
    protected $alias;

    /**
     * @var boolean $debug
     */
    protected $debug;

    /**
     * Configuration constructor.
     *
     * @param string $alias
     * @param bool   $debug
     */
    public function __construct($alias, $debug = false)
    {
        $this->alias = $alias;
        $this->debug = (boolean)$debug;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $builder->root($this->alias)
                ->children()
                ->append($this->createClientsNode())
                ->booleanNode('logging')->defaultValue($this->debug)->end()
                ->end()
                ->end();

        return $builder;
    }

    /**
     * Create Clients Configuration
     *
     * @return ArrayNodeDefinition
     * @return NodeDefinition
     * @throws \RuntimeException
     */
    private function createClientsNode()
    {
        $builder = new TreeBuilder();
        $node    = $builder->root('clients');

        // Filtering function to cast scalar values to boolean
        $boolFilter = function ($value) {
            return (bool)$value;
        };

        $node
            ->useAttributeAsKey('name')
            ->prototype('array')
            ->children()
            ->scalarNode('base_url')->defaultValue(null)->end()
            ->scalarNode('manager_class')->defaultValue('Highco\ApiConsumerBundle\Manager\Manager')->end()
            ->arrayNode('entities')
            ->prototype('array')
            ->children()
            ->scalarNode('class')->end()
            ->scalarNode('route_prefix')->end()
            ->scalarNode('repository_class')->defaultValue('Highco\ApiConsumerBundle\Repository\Repository')->end()
            ->end()
            ->end()
            ->end()
            ->arrayNode('options')
            ->children()
            ->arrayNode('headers')
            ->prototype('scalar')
            ->end()
            ->end()
            ->arrayNode('auth')
            ->prototype('scalar')
            ->end()
            ->end()
            ->arrayNode('query')
            ->prototype('scalar')
            ->end()
            ->end()
            ->variableNode('cert')
            ->validate()
            ->ifTrue(function ($v) {
                return !is_string($v) && (!is_array($v) || count($v) != 2);
            })
            ->thenInvalid('A string or a two entries array required')
            ->end()
            ->end()
            ->scalarNode('connect_timeout')->end()
            ->booleanNode('debug')
            ->beforeNormalization()
            ->ifString()->then($boolFilter)
            ->end()
            ->end()
            ->booleanNode('decode_content')
            ->beforeNormalization()
            ->ifString()->then($boolFilter)
            ->end()
            ->end()
            ->scalarNode('delay')->end()
            ->booleanNode('http_errors')
            ->beforeNormalization()
            ->ifString()->then($boolFilter)
            ->end()
            ->end()
            ->scalarNode('expect')->end()
            ->scalarNode('ssl_key')->end()
            ->booleanNode('stream')
            ->beforeNormalization()
            ->ifString()->then($boolFilter)
            ->end()
            ->end()
            ->booleanNode('synchronous')
            ->beforeNormalization()
            ->ifString()->then($boolFilter)
            ->end()
            ->end()
            ->scalarNode('timeout')->end()
            ->booleanNode('verify')
            ->beforeNormalization()
            ->ifString()->then($boolFilter)
            ->end()
            ->end()
            ->booleanNode('cookies')
            ->beforeNormalization()
            ->ifString()->then($boolFilter)
            ->end()
            ->end()
            ->scalarNode('version')->end()
            ->end()
            ->end()
            ->end()
            ->end();

        return $node;
    }
}
