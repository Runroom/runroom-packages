<?php


namespace Runroom\CookiesBundle\Tests\Integration;


use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionConfigurationTestCase;
use Runroom\CookiesBundle\DependencyInjection\Configuration;
use Runroom\CookiesBundle\DependencyInjection\RunroomCookiesExtension;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class ConfigurationTest extends AbstractExtensionConfigurationTestCase
{
    /** @test */
    public function itExposesConfiguration(): void
    {
        $this->assertProcessedConfigurationEquals([], [ __DIR__ . '/../Fixtures/configuration.yaml' ]);
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
