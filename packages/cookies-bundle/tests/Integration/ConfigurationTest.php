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

final class ConfigurationTest extends AbstractExtensionConfigurationTestCase
{
    public function testItExposesConfiguration(): void
    {
        $this->assertProcessedConfigurationEquals([
            'cookies' => [
                'mandatory_cookies' => [
                    ['name' => 'symfony', 'has_description' => true, 'cookies' => [
                        ['name' => 'PHPSESSID'], ['name' => 'client_ip'], ['name' => 'language_switched'],
                    ]],
                    ['name' => 'consent', 'has_description' => false, 'cookies' => [
                        ['name' => 'cookie_message'], ['name' => 'performance_cookie'], ['name' => 'targeting_cookie'],
                    ]],
                ],
                'performance_cookies' => [
                    ['name' => 'analytics', 'has_description' => false, 'cookies' => [
                        ['name' => '_ga'], ['name' => '_gid'],
                    ]],
                ],
                'targeting_cookies' => [
                    ['name' => 'doubleclick', 'has_description' => false, 'cookies' => [
                        ['name' => '_dc_gtm_UA-4275551-14'], ['name' => '_gat_UA-4275551-14'], ['name' => '1P_JAR', 'domain' => '.google.com'],
                    ]],
                ],
            ],
        ], [__DIR__ . '/../Fixtures/configuration.yaml']);
    }

    public function testItFailsOnInvalidConfiguration(): void
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
