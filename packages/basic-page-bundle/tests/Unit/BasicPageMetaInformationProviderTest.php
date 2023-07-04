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
use Runroom\BasicPageBundle\Factory\BasicPageFactory;
use Runroom\BasicPageBundle\Service\BasicPageMetaInformationProvider;
use Runroom\BasicPageBundle\ViewModel\BasicPageViewModel;
use Runroom\SeoBundle\Entity\EntityMetaInformation;
use Zenstruck\Foundry\Test\Factories;

final class BasicPageMetaInformationProviderTest extends TestCase
{
    use Factories;

    private BasicPage $basicPage;
    private BasicPageMetaInformationProvider $provider;
    private BasicPageViewModel $model;

    protected function setUp(): void
    {
        $this->basicPage = BasicPageFactory::createOne()->object();
        $this->provider = new BasicPageMetaInformationProvider();
        $this->model = new BasicPageViewModel($this->basicPage);
    }

    public function testItProvidesMetasForBasicPageRoutes(): void
    {
        static::assertTrue($this->provider->providesMetas('runroom.basic_page.route.show'));
    }

    public function testItReturnsEntityMetaInformationWhenValidContextIsGiven(): void
    {
        static::assertInstanceOf(EntityMetaInformation::class, $this->provider->getEntityMetaInformation(['model' => $this->model]));
        static::assertNull($this->provider->getEntityMetaInformation([]));
        static::assertNull($this->provider->getEntityMetaInformation(['model' => new \stdClass()]));
    }
}
