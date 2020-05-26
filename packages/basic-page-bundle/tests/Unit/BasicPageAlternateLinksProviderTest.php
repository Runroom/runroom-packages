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

    protected const META_ROUTE = 'runroom.static_page.route.static';

    protected $locales;
    protected $provider;

    protected function setUp(): void
    {
        $this->locales = ['es', 'en'];

        $this->provider = new BasicPageAlternateLinksProvider();
    }

    /**
     * @test
     */
    public function itReturnsRouteParameters()
    {
        $basicPage = BasicPageFixture::createWithSlugs($this->locales);

        $model = new BasicPageViewModel();
        $model->setBasicPage($basicPage);

        foreach ($this->locales as $locale) {
            $routeParameters = $this->provider->getParameters($model, $locale);

            $this->assertSame(['slug' => 'slug_' . $locale], $routeParameters);
        }
    }

    /**
     * @test
     */
    public function itProvidesAlternateLinks()
    {
        $routes = [self::META_ROUTE];

        foreach ($routes as $route) {
            $this->assertTrue($this->provider->providesAlternateLinks($route));
        }
    }
}
