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
use Prophecy\PhpUnit\ProphecyTrait;
use Runroom\BasicPageBundle\Service\BasicPageAlternateLinksProvider;
use Runroom\BasicPageBundle\Tests\Fixtures\BasicPageFixture;
use Runroom\BasicPageBundle\ViewModel\BasicPageViewModel;

class BasicPageAlternateLinksProviderTest extends TestCase
{
    use ProphecyTrait;

    /** @var string[] */
    private $locales = ['es', 'en'];

    /** @var BasicPageAlternateLinksProvider */
    private $provider;

    protected function setUp(): void
    {
        $this->provider = new BasicPageAlternateLinksProvider();
    }

    /** @test */
    public function itReturnsAvailableLocales(): void
    {
        $model = new BasicPageViewModel();
        $model->setBasicPage(BasicPageFixture::createWithSlugs($this->locales));

        self::assertSame(['es', 'en'], $this->provider->getAvailableLocales($model));
    }

    /** @test */
    public function itReturnsRouteParameters(): void
    {
        $model = new BasicPageViewModel();
        $model->setBasicPage(BasicPageFixture::createWithSlugs($this->locales));

        foreach ($this->locales as $locale) {
            $routeParameters = $this->provider->getParameters($model, $locale);

            self::assertSame(['slug' => 'slug_' . $locale], $routeParameters);
        }
    }

    /** @test */
    public function itProvidesAlternateLinks(): void
    {
        self::assertTrue($this->provider->providesAlternateLinks('runroom.basic_page.route.show'));
    }
}
