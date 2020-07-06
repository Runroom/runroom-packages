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

namespace Runroom\SeoBundle\Tests\Integration;

use Runroom\SeoBundle\MetaInformation\MetaInformationBuilder;
use Runroom\SeoBundle\Repository\MetaInformationRepository;
use Runroom\Testing\TestCase\DoctrineTestCase;

class MetaInformationRepositoryTest extends DoctrineTestCase
{
    /** @var MetaInformationRepository */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = static::$container->get(MetaInformationRepository::class);
    }

    /** @test */
    public function itFindsDefaultMetaInformation(): void
    {
        $metaInformation = $this->repository->findOneBy(['route' => MetaInformationBuilder::DEFAULT_ROUTE]);

        if (null !== $metaInformation) {
            self::assertSame(1, $metaInformation->getId());
            self::assertSame(MetaInformationBuilder::DEFAULT_ROUTE, $metaInformation->getRoute());
            self::assertNotNull($metaInformation->getRouteName());
            self::assertNotNull($metaInformation->getTitle());
            self::assertNotNull($metaInformation->getDescription());
        } else {
            self::fail('not found metaInformation');
        }
    }

    /** @test */
    public function itFindsRouteMetaInformation(): void
    {
        $metaInformation = $this->repository->findOneBy(['route' => 'test']);

        if (null !== $metaInformation) {
            self::assertSame(2, $metaInformation->getId());
        } else {
            self::fail('not found metaInformation');
        }
    }

    protected function getDataFixtures(): array
    {
        return ['meta_informations.yaml'];
    }
}
