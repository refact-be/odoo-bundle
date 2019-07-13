<?php

namespace Refact\OdooBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('odoo');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('url')->isRequired()->end()
                ->scalarNode('database')->isRequired()->end()
                ->integerNode('admin_id')->isRequired()->end()
                ->scalarNode('admin_pass')->isRequired()->end()
                ->scalarNode('sso_secret')->end()
                ->arrayNode('role_mapping')->defaultValue([])
                    ->scalarPrototype()->isRequired()->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
