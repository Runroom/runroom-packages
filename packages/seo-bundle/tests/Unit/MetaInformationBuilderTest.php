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

use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Runroom\SeoBundle\Factory\MetaInformationFactory;
use Runroom\SeoBundle\MetaInformation\AbstractMetaInformationProvider;
use Runroom\SeoBundle\MetaInformation\MetaInformationBuilder;
use Runroom\SeoBundle\Repository\MetaInformationRepository;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Zenstruck\Foundry\Test\Factories;

class MetaInformationBuilderTest extends TestCase
{
    use Factories;

    /** @var Stub&MetaInformationRepository */
    private $repository;

    /** @var MetaInformationBuilder */
    private $builder;

    protected function setUp(): void
    {
        $this->repository = $this->createStub(MetaInformationRepository::class);

        $this->builder = new MetaInformationBuilder(
            $this->repository,
            PropertyAccess::createPropertyAccessor()
        );
    }

    /** @test */
    public function itBuildsMetaInformationViewModel(): void
    {
        $model = new \stdClass();
        $model->placeholder = 'test';

        $metaInformation = MetaInformationFactory::new()->withTranslations(['en'], [
            'title' => '[placeholder] title',
            'description' => '[missing] description',
        ])->create()->object();

        $this->repository->method('findOneBy')->willReturn($metaInformation);

        $metas = $this->builder->build(new TestMetaInformationProvider(), 'test', $model);

        self::assertSame('test title', $metas->getTitle());
        self::assertSame(' description', $metas->getDescription());
        self::assertNull($metas->getImage());
    }
}

class TestMetaInformationProvider extends AbstractMetaInformationProvider
{
    protected function getRoutes(): array
    {
        return ['test'];
    }

    protected function getRouteAliases(): array
    {
        return [
            'default' => ['test'],
        ];
    }
}
