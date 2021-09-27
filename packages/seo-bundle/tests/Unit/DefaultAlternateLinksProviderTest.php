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
use Runroom\SeoBundle\Tests\App\ViewModel\DummyViewModel;

class DefaultAlternateLinksProviderTest extends TestCase
{
    private DefaultAlternateLinksProvider $provider;

    protected function setUp(): void
    {
        $this->provider = new DefaultAlternateLinksProvider();
    }

    /** @test */
    public function itProvidesMetasForAnyRoute(): void
    {
        foreach (['default', 'home'] as $route) {
            static::assertTrue($this->provider->providesAlternateLinks($route));
        }
    }

    /** @test */
    public function itDoesNotDefineRouteParameters(): void
    {
        static::assertNull($this->provider->getParameters(['model' => new DummyViewModel()], 'es'));
    }

    /** @test */
    public function itDoesNotDefineAvailableLocales(): void
    {
        $context = ['model' => new DummyViewModel()];

        static::assertTrue($this->provider->canGenerateAlternateLink($context, 'random_lang'));
        static::assertTrue($this->provider->canGenerateAlternateLink($context, 'es'));
    }

    /** @test */
    public function itDoesNotDefineAssociatedRoutes(): void
    {
        $method = new \ReflectionMethod($this->provider, 'getRoutes');
        $method->setAccessible(true);

        static::assertEmpty($method->invoke($this->provider));
    }
}
