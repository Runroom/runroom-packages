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
use Prophecy\PhpUnit\ProphecyTrait;
use Runroom\SeoBundle\Entity\EntityMetaInformation;
use Runroom\SeoBundle\Tests\Fixtures\MetaInformationAwareEntity;

class MetaInformationAwareTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function itSetsAndGetsMetaInformation()
    {
        $entityMetaInformation = $this->prophesize(EntityMetaInformation::class);
        $metaInformationAware = new MetaInformationAwareEntity();

        $expected = $entityMetaInformation->reveal();
        $metaInformationAware = $metaInformationAware->setMetaInformation($expected);

        $this->assertSame($expected, $metaInformationAware->getMetaInformation());
    }
}
