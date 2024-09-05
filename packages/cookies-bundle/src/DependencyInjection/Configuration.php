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

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @phpstan-type CookiesData = array<string, array{
 *     name: string,
 *     has_description?: bool,
 *     cookies: string[]
 * }[]>
 */
final class Configuration implements ConfigurationInterface
{
    /**
     * @psalm-suppress UndefinedInterfaceMethod
     *
     * @see https://github.com/psalm/psalm-plugin-symfony/issues/174
     */
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

    /**
     * @psalm-suppress UndefinedInterfaceMethod
     *
     * @see https://github.com/psalm/psalm-plugin-symfony/issues/174
     */
    private function addCookiesSection(string $name): NodeDefinition
    {
        $treeBuilder = new TreeBuilder($name);
        $rootNode = $treeBuilder->getRootNode();

        return $rootNode
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
    }
}
