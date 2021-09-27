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
use Runroom\SeoBundle\MetaInformation\DefaultMetaInformationProvider;
use Runroom\SeoBundle\Tests\App\ViewModel\DummyViewModel;

class DefaultMetaInformationProviderTest extends TestCase
{
    private DefaultMetaInformationProvider $provider;

    protected function setUp(): void
    {
        $this->provider = new DefaultMetaInformationProvider();
    }

    /** @test */
    public function itProvidesMetasForAnyRoute(): void
    {
        foreach (['default', 'home'] as $route) {
            static::assertTrue($this->provider->providesMetas($route));
        }
    }

    /** @test */
    public function itDoesNotDefineAnyAlias(): void
    {
        static::assertSame('default', $this->provider->getRouteAlias('default'));
    }

    /** @test */
    public function itDoesNotDefineEntityMetaInformation(): void
    {
        static::assertNull($this->provider->getEntityMetaInformation(['model' => new DummyViewModel()]));
    }

    /** @test */
    public function itDoesNotDefineEntityMetaImage(): void
    {
        static::assertNull($this->provider->getEntityMetaImage(['model' => new DummyViewModel()]));
    }

    /** @test */
    public function itDoesNotDefineAssociatedRoutes(): void
    {
        $method = new \ReflectionMethod($this->provider, 'getRoutes');
        $method->setAccessible(true);

        static::assertEmpty($method->invoke($this->provider));
    }
}
