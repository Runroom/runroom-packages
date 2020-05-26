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
    /**
     * @test
     */
    public function itGetsProperties()
    {
        $metaInformation = MetaInformationFixture::create();

        $this->assertSame(MetaInformationFixture::ROUTE_NAME, $metaInformation->__toString());
        $this->assertSame(MetaInformationFixture::ID, $metaInformation->getId());
        $this->assertSame(MetaInformationFixture::ROUTE, $metaInformation->getRoute());
        $this->assertSame(MetaInformationFixture::ROUTE_NAME, $metaInformation->getRouteName());
        $this->assertSame(MetaInformationFixture::IMAGE, $metaInformation->getImage());
        $this->assertSame(MetaInformationFixture::TITLE, $metaInformation->getTitle());
        $this->assertSame(MetaInformationFixture::DESCRIPTION, $metaInformation->getDescription());
    }
}
