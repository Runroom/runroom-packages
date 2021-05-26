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
use Runroom\SeoBundle\Factory\EntityMetaInformationFactory;
use Zenstruck\Foundry\Test\Factories;

class EntityMetaInformationTest extends TestCase
{
    use Factories;

    /** @test */
    public function itGetsProperties(): void
    {
        $metaInformation = EntityMetaInformationFactory::new()->withTranslations(['en'])->create();

        self::assertNotEmpty((string) $metaInformation);
        self::assertNull($metaInformation->getId());
        self::assertNotNull($metaInformation->getTitle());
        self::assertNotNull($metaInformation->getDescription());
    }
}
