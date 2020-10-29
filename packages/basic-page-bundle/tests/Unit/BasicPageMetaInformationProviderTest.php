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
use Runroom\BasicPageBundle\Entity\BasicPage;
use Runroom\BasicPageBundle\Service\BasicPageMetaInformationProvider;
use Runroom\BasicPageBundle\Tests\Fixtures\BasicPageFixture;
use Runroom\BasicPageBundle\ViewModel\BasicPageViewModel;
use Runroom\SeoBundle\Entity\EntityMetaInformation;

class BasicPageMetaInformationProviderTest extends TestCase
{
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
        self::assertTrue($this->provider->providesMetas('runroom.basic_page.route.show'));
    }

    /** @test */
    public function itHasAnEntityMetaInformation(): void
    {
        $entityMetas = $this->provider->getEntityMetaInformation($this->model);

        self::assertInstanceOf(EntityMetaInformation::class, $entityMetas);
    }
}
