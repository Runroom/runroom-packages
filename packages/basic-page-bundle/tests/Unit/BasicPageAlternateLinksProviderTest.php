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

namespace Runroom\BasicPageBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Runroom\BasicPageBundle\Factory\BasicPageFactory;
use Runroom\BasicPageBundle\Service\BasicPageAlternateLinksProvider;
use Runroom\BasicPageBundle\ViewModel\BasicPageViewModel;
use Zenstruck\Foundry\Test\Factories;

class BasicPageAlternateLinksProviderTest extends TestCase
{
    use Factories;

    private BasicPageAlternateLinksProvider $provider;

    protected function setUp(): void
    {
        $this->provider = new BasicPageAlternateLinksProvider();
    }

    /**
     * @test
     */
    public function itCanGenerateAlternateLinksIfValidContext(): void
    {
        $basicPage = BasicPageFactory::new()->withTranslations(['en', 'es'])->create()->object();
        $context = ['model' => new BasicPageViewModel($basicPage)];

        static::assertTrue($this->provider->canGenerateAlternateLink($context, 'es'));
        static::assertTrue($this->provider->canGenerateAlternateLink($context, 'en'));
        static::assertFalse($this->provider->canGenerateAlternateLink([], 'es'));
        static::assertFalse($this->provider->canGenerateAlternateLink(['model' => new \stdClass()], 'es'));
    }

    /**
     * @test
     */
    public function itReturnsRouteParameters(): void
    {
        $basicPage = BasicPageFactory::new()->withTranslations(['en', 'es'])->create()->object();
        $context = ['model' => new BasicPageViewModel($basicPage)];

        foreach (['en', 'es'] as $locale) {
            static::assertSame(
                ['slug' => $basicPage->getSlug($locale)],
                $this->provider->getParameters($context, $locale)
            );
        }
    }

    /**
     * @test
     */
    public function itReturnsNullParametersForAnInvalidContext(): void
    {
        static::assertNull($this->provider->getParameters([], 'es'));
        static::assertNull($this->provider->getParameters(['model' => new \stdClass()], 'es'));
    }

    /**
     * @test
     */
    public function itProvidesAlternateLinks(): void
    {
        static::assertTrue($this->provider->providesAlternateLinks('runroom.basic_page.route.show'));
    }
}
