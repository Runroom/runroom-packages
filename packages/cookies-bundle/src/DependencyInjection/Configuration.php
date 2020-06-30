<?php

declare(strict_types=1);

/*
 * This file is part of the Runroom package.
 *
 * (c) Runroom <runroom@runroom.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Runroom\CookiesBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('runroom_cookies');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode->children()
            ->arrayNode('cookies')
                ->isRequired()
                ->append($this->addCookiesSection('mandatory_cookies'))
                ->append($this->addCookiesSection('performance_cookies'))
                ->append($this->addCookiesSection('targeting_cookies'))
            ->end()
        ->end();

        return $treeBuilder;
    }

    private function addCookiesSection(string $name)
    {
        $treeBuilder = new TreeBuilder($name);
        $rootNode = $treeBuilder->getRootNode();

        $node = $rootNode
            ->isRequired()
            ->requiresAtLeastOneElement()
            ->arrayPrototype()
                ->children()
                    ->scalarNode('name')->isRequired()->cannotBeEmpty()->end()
                    ->booleanNode('has_description')->defaultFalse()->end()
                    ->arrayNode('cookies')
                        ->isRequired()
                        ->requiresAtLeastOneElement()
                        ->arrayPrototype()
                            ->children()
                                ->scalarNode('name')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('domain')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }
}
