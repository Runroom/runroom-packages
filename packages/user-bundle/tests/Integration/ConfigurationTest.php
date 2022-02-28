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

namespace Runroom\UserBundle\Tests\Integration;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;
use Runroom\UserBundle\DependencyInjection\Configuration;
use Runroom\UserBundle\DependencyInjection\RunroomUserExtension;
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
            'reset_password' => [
                'enabled' => false,
                'lifetime' => 3600,
                'throttle_limit' => 3600,
                'enable_garbage_collection' => true,
                'email' => [
                    'address' => 'admin@localhost',
                    'sender_name' => 'Administration',
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
        return new RunroomUserExtension();
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration();
    }
}
