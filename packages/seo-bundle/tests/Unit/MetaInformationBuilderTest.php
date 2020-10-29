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
use Runroom\SeoBundle\MetaInformation\AbstractMetaInformationProvider;
use Runroom\SeoBundle\MetaInformation\MetaInformationBuilder;
use Runroom\SeoBundle\Repository\MetaInformationRepository;
use Runroom\SeoBundle\Tests\Fixtures\MetaInformationFixture;
use Symfony\Component\PropertyAccess\PropertyAccess;

class MetaInformationBuilderTest extends TestCase
{
    /** @var Stub&MetaInformationRepository */
    private $repository;

    /** @var MetaInformationBuilder */
    private $builder;

    protected function setUp(): void
    {
        $this->repository = $this->createStub(MetaInformationRepository::class);
        $this->repository->method('findOneBy')->willReturn(MetaInformationFixture::create());

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
