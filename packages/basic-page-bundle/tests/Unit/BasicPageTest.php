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
use Runroom\BasicPageBundle\Tests\Fixtures\BasicPageFixture;
use Runroom\SeoBundle\Entity\EntityMetaInformation;

class BasicPageTest extends TestCase
{
    /** @test */
    public function itGetsProperties(): void
    {
        $BasicPage = BasicPageFixture::create();

        self::assertSame(BasicPageFixture::TITLE, $BasicPage->__toString());
        self::assertSame(BasicPageFixture::ID, $BasicPage->getId());
        self::assertSame(BasicPageFixture::TITLE, $BasicPage->getTitle());
        self::assertSame(BasicPageFixture::LOCATION, $BasicPage->getLocation());
        self::assertSame(BasicPageFixture::CONTENT, $BasicPage->getContent());
        self::assertSame(BasicPageFixture::SLUG, $BasicPage->getSlug());
        self::assertSame(BasicPageFixture::PUBLISH, $BasicPage->getPublish());
        self::assertInstanceOf(EntityMetaInformation::class, $BasicPage->getMetaInformation());
    }
}
