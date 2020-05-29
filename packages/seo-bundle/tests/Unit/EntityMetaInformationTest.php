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
use Runroom\SeoBundle\Tests\Fixtures\EntityMetaInformationFixture;

class EntityMetaInformationTest extends TestCase
{
    /** @test */
    public function itGetsProperties(): void
    {
        $metaInformation = EntityMetaInformationFixture::create();

        $this->assertSame(EntityMetaInformationFixture::TITLE, $metaInformation->__toString());
        $this->assertSame(EntityMetaInformationFixture::ID, $metaInformation->getId());
        $this->assertSame(EntityMetaInformationFixture::TITLE, $metaInformation->getTitle());
        $this->assertSame(EntityMetaInformationFixture::DESCRIPTION, $metaInformation->getDescription());
    }
}
