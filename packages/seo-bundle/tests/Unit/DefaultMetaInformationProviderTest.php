<?php

/*
 * This file is part of the Runroom package.
 *
 * (c) Runroom <runroom@runroom.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Runroom\SeoBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Runroom\SeoBundle\MetaInformation\DefaultMetaInformationProvider;

class DefaultMetaInformationProviderTest extends TestCase
{
    protected $provider;

    protected function setUp(): void
    {
        $this->provider = new DefaultMetaInformationProvider();
    }

    /**
     * @test
     */
    public function itProvidesMetasForAnyRoute()
    {
        foreach (['default', 'home'] as $route) {
            $this->assertTrue($this->provider->providesMetas($route));
        }
    }

    /**
     * @test
     */
    public function itDoesNotDefineAnyAlias()
    {
        $this->assertSame('default', $this->provider->getRouteAlias('default'));
    }

    /**
     * @test
     */
    public function itDoesNotDefinePlaceholders()
    {
        $this->assertEmpty($this->provider->getPlaceholders(new \stdClass()));
    }

    /**
     * @test
     */
    public function itDoesNotDefineEntityMetaInformation()
    {
        $this->assertNull($this->provider->getEntityMetaInformation(new \stdClass()));
    }

    /**
     * @test
     */
    public function itDoesNotDefineEntityMetaImage()
    {
        $this->assertNull($this->provider->getEntityMetaImage(new \stdClass()));
    }

    /**
     * @test
     */
    public function itDoesNotDefineAssociatedRoutes()
    {
        $method = new \ReflectionMethod($this->provider, 'getRoutes');
        $method->setAccessible(true);

        $this->assertEmpty($method->invoke($this->provider));
    }
}
