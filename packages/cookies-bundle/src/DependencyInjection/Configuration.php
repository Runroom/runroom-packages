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
                ->arrayPrototype()->children()
                    ->arrayNode('name')->children()
                        ->booleanNode('has_description')->end()
                        ->arrayNode('cookies')->children()
                            ->scalarNode('name')->end()
                            ->scalarNode('domain')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end()
        ;

        return $treeBuilder;
    }
}
