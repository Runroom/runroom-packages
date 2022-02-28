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

namespace Runroom\RedirectionBundle\Tests\Integration;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;
use Runroom\RedirectionBundle\DependencyInjection\Configuration;
use Runroom\RedirectionBundle\DependencyInjection\RunroomRedirectionExtension;
use Runroom\RedirectionBundle\Tests\App\Entity\Entity;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class ConfigurationTest extends AbstractExtensionConfigurationTestCase
{
    /**
     * @test
     */
    public function itExposesConfiguration(): void
    {
        $this->assertProcessedConfigurationEquals([
            'enable_automatic_redirections' => true,
            'automatic_redirections' => [
                Entity::class => [
                    'route' => 'test',
                    'routeParameters' => [
                        'slug' => 'slug',
                    ],
                ],
            ],
        ], [
            __DIR__ . '/../Fixtures/configuration.yaml',
        ]);
    }

    /**
     * @test
     */
    public function itFailsOnInvalidConfiguration(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->assertProcessedConfigurationEquals([], [
            __DIR__ . '/../Fixtures/configuration_invalid.yaml',
        ]);
    }

    protected function getContainerExtension(): ExtensionInterface
    {
        return new RunroomRedirectionExtension();
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration();
    }
}
