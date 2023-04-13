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
use Runroom\SeoBundle\MetaInformation\MetaInformationBuilder;
use Runroom\SeoBundle\Repository\MetaInformationRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class MetaInformationRepositoryTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    private MetaInformationRepository $repository;

    protected function setUp(): void
    {
        parent::bootKernel();

        /**
         * @todo: Simplify this when dropping support for Symfony 4
         *
         * @phpstan-ignore-next-line
         */
        $container = method_exists(static::class, 'getContainer') ? static::getContainer() : static::$container;

        $this->repository = $container->get(MetaInformationRepository::class);
    }

    /**
     * @test
     */
    public function itFindsDefaultMetaInformation(): void
    {
        MetaInformationFactory::new(['route' => MetaInformationBuilder::DEFAULT_ROUTE])->withTranslations(['en'])->create();

        $metaInformation = $this->repository->findOneBy(['route' => MetaInformationBuilder::DEFAULT_ROUTE]);

        if (null !== $metaInformation) {
            static::assertSame(1, $metaInformation->getId());
            static::assertNotEmpty((string) $metaInformation);
            static::assertSame(MetaInformationBuilder::DEFAULT_ROUTE, $metaInformation->getRoute());
            static::assertNull($metaInformation->getImage());
            static::assertNotNull($metaInformation->getRouteName());
            static::assertNotNull($metaInformation->getDescription());
        } else {
            static::fail('not found metaInformation');
        }
    }

    /**
     * @test
     */
    public function itFindsRouteMetaInformation(): void
    {
        MetaInformationFactory::createOne(['route' => 'test']);

        $metaInformation = $this->repository->findOneBy(['route' => 'test']);

        if (null !== $metaInformation) {
            static::assertSame(1, $metaInformation->getId());
        } else {
            static::fail('not found metaInformation');
        }
    }
}
