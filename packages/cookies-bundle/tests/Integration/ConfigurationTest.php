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

namespace Runroom\CookiesBundle\Tests\Integration;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;
use Runroom\CookiesBundle\DependencyInjection\Configuration;
use Runroom\CookiesBundle\DependencyInjection\RunroomCookiesExtension;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class ConfigurationTest extends AbstractExtensionConfigurationTestCase
{
    /** @test */
    public function itExposesConfiguration(): void
    {
        $this->assertProcessedConfigurationEquals([], [__DIR__ . '/../Fixtures/configuration.yaml']);
    }

    /** @test */
    public function itFailsOnInvalidConfiguration(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->assertProcessedConfigurationEquals([], [__DIR__ . '/../Fixtures/invalid_configuration.yaml']);
    }

    protected function getContainerExtension(): ExtensionInterface
    {
        return new RunroomCookiesExtension();
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration();
    }
}
