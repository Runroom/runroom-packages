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

    /** @var BasicPageAlternateLinksProvider */
    private $provider;

    protected function setUp(): void
    {
        $this->provider = new BasicPageAlternateLinksProvider();
    }

    /** @test */
    public function itReturnsAvailableLocales(): void
    {
        $basicPage = BasicPageFactory::createOne()->object();
        $model = new BasicPageViewModel();
        $model->setBasicPage($basicPage);

        self::assertCount($basicPage->getTranslations()->count(), $this->provider->getAvailableLocales($model));
    }

    /** @test */
    public function itReturnsRouteParameters(): void
    {
        $basicPage = BasicPageFactory::createOne()->object();
        $model = new BasicPageViewModel();
        $model->setBasicPage($basicPage);

        foreach ($basicPage->getTranslations()->getKeys() as $locale) {
            self::assertSame(
                ['slug' => $basicPage->translate($locale)->getSlug()],
                $this->provider->getParameters($model, $locale)
            );
        }
    }

    /** @test */
    public function itProvidesAlternateLinks(): void
    {
        self::assertTrue($this->provider->providesAlternateLinks('runroom.basic_page.route.show'));
    }
}
