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
use Runroom\BasicPageBundle\Entity\BasicPage;
use Runroom\BasicPageBundle\Service\BasicPageMetaInformationProvider;
use Runroom\BasicPageBundle\Tests\Fixtures\BasicPageFixture;
use Runroom\BasicPageBundle\ViewModel\BasicPageViewModel;
use Runroom\SeoBundle\Entity\EntityMetaInformation;

class BasicPageMetaInformationProviderTest extends TestCase
{
    use ProphecyTrait;

    private const META_ROUTE = 'runroom.basic_page.route.show';

    /** @var BasicPage */
    private $basicPage;

    /** @var BasicPageMetaInformationProvider */
    private $provider;

    /** @var BasicPageViewModel */
    private $model;

    protected function setUp(): void
    {
        $this->basicPage = BasicPageFixture::create();
        $this->provider = new BasicPageMetaInformationProvider();

        $this->model = new BasicPageViewModel();
        $this->model->setBasicPage($this->basicPage);
    }

    /** @test */
    public function itProvidesMetasForBasicPageRoutes(): void
    {
        $routes = [self::META_ROUTE];

        foreach ($routes as $route) {
            $this->assertTrue($this->provider->providesMetas($route));
        }
    }

    /** @test */
    public function itHasPlaceholders(): void
    {
        $expectedPlaceholders = [
            '{title}' => BasicPageFixture::TITLE,
            '{content}' => BasicPageFixture::CONTENT,
        ];

        $placeholders = $this->provider->getPlaceholders($this->model);

        $this->assertSame($expectedPlaceholders, $placeholders);
    }

    /** @test */
    public function itHasAnEntityMetaInformation(): void
    {
        $entityMetas = $this->provider->getEntityMetaInformation($this->model);

        $this->assertInstanceOf(EntityMetaInformation::class, $entityMetas);
    }
}
