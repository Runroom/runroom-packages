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

namespace Runroom\UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * @psalm-suppress PossiblyNullReference, UndefinedInterfaceMethod
     *
     * @see https://github.com/psalm/psalm-plugin-symfony/issues/174
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('runroom_user');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode->children()
            ->arrayNode('reset_password')
                ->canBeEnabled()
                ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('email')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('address')
                                ->cannotBeEmpty()
                                ->defaultValue('admin@localhost')
                            ->end()
                            ->scalarNode('sender_name')
                                ->cannotBeEmpty()
                                ->defaultValue('Administration')
                            ->end()
                        ->end()
                    ->end()
                    ->integerNode('lifetime')->defaultValue(3600)->end()
                    ->integerNode('throttle_limit')->defaultValue(3600)->end()
                    ->booleanNode('enable_garbage_collection')->defaultValue(true)->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
