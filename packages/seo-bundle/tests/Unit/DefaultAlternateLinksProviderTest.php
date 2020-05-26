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

namespace Runroom\SeoBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Runroom\SeoBundle\AlternateLinks\DefaultAlternateLinksProvider;

class DefaultAlternateLinksProviderTest extends TestCase
{
    protected $provider;

    protected function setUp(): void
    {
        $this->provider = new DefaultAlternateLinksProvider();
    }

    /**
     * @test
     */
    public function itProvidesMetasForAnyRoute()
    {
        foreach (['default', 'home'] as $route) {
            $this->assertTrue($this->provider->providesAlternateLinks($route));
        }
    }

    /**
     * @test
     */
    public function itDoesNotDefineRouteParameters()
    {
        $this->assertNull($this->provider->getParameters(new \stdClass(), 'es'));
    }

    /**
     * @test
     */
    public function itDoesNotDefineAvailableLocales()
    {
        $this->assertNull($this->provider->getAvailableLocales(new \stdClass()));
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
