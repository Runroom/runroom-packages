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

    private const META_ROUTE = 'runroom.basic_page.route.show';

    /** @var array */
    private $locales;

    /** @var BasicPageAlternateLinksProvider */
    private $provider;

    protected function setUp(): void
    {
        $this->locales = ['es', 'en'];

        $this->provider = new BasicPageAlternateLinksProvider();
    }

    /** @test */
    public function itReturnsRouteParameters(): void
    {
        $basicPage = BasicPageFixture::createWithSlugs($this->locales);

        $model = new BasicPageViewModel();
        $model->setBasicPage($basicPage);

        foreach ($this->locales as $locale) {
            $routeParameters = $this->provider->getParameters($model, $locale);

            $this->assertSame(['slug' => 'slug_' . $locale], $routeParameters);
        }
    }

    /** @test */
    public function itProvidesAlternateLinks(): void
    {
        $routes = [self::META_ROUTE];

        foreach ($routes as $route) {
            $this->assertTrue($this->provider->providesAlternateLinks($route));
        }
    }
}
