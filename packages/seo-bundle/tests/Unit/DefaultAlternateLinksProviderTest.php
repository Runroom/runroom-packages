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
    /** @var DefaultAlternateLinksProvider */
    private $provider;

    protected function setUp(): void
    {
        $this->provider = new DefaultAlternateLinksProvider();
    }

    /** @test */
    public function itProvidesMetasForAnyRoute(): void
    {
        foreach (['default', 'home'] as $route) {
            self::assertTrue($this->provider->providesAlternateLinks($route));
        }
    }

    /** @test */
    public function itDoesNotDefineRouteParameters(): void
    {
        self::assertNull($this->provider->getParameters(new \stdClass(), 'es'));
    }

    /** @test */
    public function itDoesNotDefineAvailableLocales(): void
    {
        self::assertNull($this->provider->getAvailableLocales(new \stdClass()));
    }

    /** @test */
    public function itDoesNotDefineAssociatedRoutes(): void
    {
        $method = new \ReflectionMethod($this->provider, 'getRoutes');
        $method->setAccessible(true);

        self::assertEmpty($method->invoke($this->provider));
    }
}
