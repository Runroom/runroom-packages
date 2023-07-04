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

final class DefaultAlternateLinksProviderTest extends TestCase
{
    private DefaultAlternateLinksProvider $provider;

    protected function setUp(): void
    {
        $this->provider = new DefaultAlternateLinksProvider();
    }

    public function testItProvidesMetasForAnyRoute(): void
    {
        foreach (['default', 'home'] as $route) {
            static::assertTrue($this->provider->providesAlternateLinks($route));
        }
    }

    public function testItDoesNotDefineRouteParameters(): void
    {
        static::assertNull($this->provider->getParameters(['model' => new DummyViewModel()], 'es'));
    }

    public function testItDoesNotDefineAvailableLocales(): void
    {
        $context = ['model' => new DummyViewModel()];

        static::assertTrue($this->provider->canGenerateAlternateLink($context, 'random_lang'));
        static::assertTrue($this->provider->canGenerateAlternateLink($context, 'es'));
    }

    public function testItDoesNotDefineAssociatedRoutes(): void
    {
        $method = new \ReflectionMethod($this->provider, 'getRoutes');
        $method->setAccessible(true);

        static::assertEmpty($method->invoke($this->provider));
    }
}
