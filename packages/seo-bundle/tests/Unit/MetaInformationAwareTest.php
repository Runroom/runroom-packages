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
use Runroom\SeoBundle\Entity\EntityMetaInformation;
use Runroom\SeoBundle\Tests\App\Entity\MetaInformationAwareEntity;

final class MetaInformationAwareTest extends TestCase
{
    public function testItSetsAndGetsMetaInformation(): void
    {
        $entityMetaInformation = $this->createStub(EntityMetaInformation::class);
        $metaInformationAware = new MetaInformationAwareEntity();

        $metaInformationAware = $metaInformationAware->setMetaInformation($entityMetaInformation);

        static::assertSame($entityMetaInformation, $metaInformationAware->getMetaInformation());
    }
}
