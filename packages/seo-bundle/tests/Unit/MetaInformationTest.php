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
use Runroom\SeoBundle\Tests\Fixtures\MetaInformationFixture;

class MetaInformationTest extends TestCase
{
    /** @test */
    public function itGetsProperties(): void
    {
        $metaInformation = MetaInformationFixture::create();

        self::assertSame(MetaInformationFixture::ROUTE_NAME, $metaInformation->__toString());
        self::assertSame(MetaInformationFixture::ID, $metaInformation->getId());
        self::assertSame(MetaInformationFixture::ROUTE, $metaInformation->getRoute());
        self::assertSame(MetaInformationFixture::ROUTE_NAME, $metaInformation->getRouteName());
        self::assertSame(MetaInformationFixture::IMAGE, $metaInformation->getImage());
        self::assertSame(MetaInformationFixture::TITLE, $metaInformation->getTitle());
        self::assertSame(MetaInformationFixture::DESCRIPTION, $metaInformation->getDescription());
    }
}
