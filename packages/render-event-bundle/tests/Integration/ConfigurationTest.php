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

namespace Runroom\RenderEventBundle\Tests\Integration;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;
use Runroom\RenderEventBundle\DependencyInjection\Configuration;
use Runroom\RenderEventBundle\DependencyInjection\RunroomRenderEventExtension;
use Runroom\RenderEventBundle\ViewModel\PageViewModel;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class ConfigurationTest extends AbstractExtensionConfigurationTestCase
{
    /**
     * @test
     */
    public function itExposesConfiguration()
    {
        $this->assertProcessedConfigurationEquals([
            'page_view_model' => PageViewModel::class,
        ], [
            __DIR__ . '/../Fixtures/configuration.yaml',
        ]);
    }

    /**
     * @test
     */
    public function itFailsOnInvalidConfiguration()
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->assertProcessedConfigurationEquals([], [
            __DIR__ . '/../Fixtures/configuration_invalid.yaml',
        ]);
    }

    protected function getContainerExtension(): ExtensionInterface
    {
        return new RunroomRenderEventExtension();
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration();
    }
}
