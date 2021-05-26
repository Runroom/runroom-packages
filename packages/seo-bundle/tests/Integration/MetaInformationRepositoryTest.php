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

use Runroom\SeoBundle\Factory\MetaInformationFactory;
use Runroom\SeoBundle\Factory\MetaInformationTranslationFactory;
use Runroom\SeoBundle\MetaInformation\MetaInformationBuilder;
use Runroom\SeoBundle\Repository\MetaInformationRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class MetaInformationRepositoryTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    /** @var MetaInformationRepository */
    private $repository;

    protected function setUp(): void
    {
        parent::bootKernel();

        $this->repository = static::$container->get(MetaInformationRepository::class);
    }

    /** @test */
    public function itFindsDefaultMetaInformation(): void
    {
        MetaInformationFactory::createOne([
            'route' => MetaInformationBuilder::DEFAULT_ROUTE,
            'translations' => MetaInformationTranslationFactory::createMany(1, [
                'locale' => 'en',
            ]),
        ]);

        $metaInformation = $this->repository->findOneBy(['route' => MetaInformationBuilder::DEFAULT_ROUTE]);

        if (null !== $metaInformation) {
            self::assertSame(1, $metaInformation->getId());
            self::assertNotEmpty((string) $metaInformation);
            self::assertSame(MetaInformationBuilder::DEFAULT_ROUTE, $metaInformation->getRoute());
            self::assertNull($metaInformation->getImage());
            self::assertNotNull($metaInformation->getRouteName());
            self::assertNotNull($metaInformation->getDescription());
        } else {
            self::fail('not found metaInformation');
        }
    }

    /** @test */
    public function itFindsRouteMetaInformation(): void
    {
        MetaInformationFactory::createOne(['route' => 'test']);

        $metaInformation = $this->repository->findOneBy(['route' => 'test']);

        if (null !== $metaInformation) {
            self::assertSame(1, $metaInformation->getId());
        } else {
            self::fail('not found metaInformation');
        }
    }
}
