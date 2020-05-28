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
    /**
     * @test
     */
    public function itGetsProperties(): void
    {
        $BasicPage = BasicPageFixture::create();

        $this->assertSame(BasicPageFixture::TITLE, $BasicPage->__toString());
        $this->assertSame(BasicPageFixture::ID, $BasicPage->getId());
        $this->assertSame(BasicPageFixture::TITLE, $BasicPage->getTitle());
        $this->assertSame(BasicPageFixture::LOCATION, $BasicPage->getLocation());
        $this->assertSame(BasicPageFixture::CONTENT, $BasicPage->getContent());
        $this->assertSame(BasicPageFixture::SLUG, $BasicPage->getSlug());
        $this->assertSame(BasicPageFixture::PUBLISH, $BasicPage->getPublish());
        $this->assertInstanceOf(EntityMetaInformation::class, $BasicPage->getMetaInformation());
    }
}
