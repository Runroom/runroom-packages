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

namespace Runroom\RedirectionBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('runroom_redirection');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode->children()
            ->booleanNode('enable_automatic_redirections')->defaultFalse()->end()
            ->arrayNode('automatic_redirections')
                ->validate()
                    ->ifTrue(function ($config) {
                        foreach (array_keys($config) as $entity) {
                            if (!class_exists($entity)) {
                                return true;
                            }
                        }

                        return false;
                    })
                    ->thenInvalid('The class does not exist')
                ->end()
                ->arrayPrototype()
                    ->children()
                        ->scalarNode('route')->isRequired()->cannotBeEmpty()->end()
                        ->arrayNode('routeParameters')
                            ->isRequired()
                            ->requiresAtLeastOneElement()
                            ->scalarPrototype()->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
