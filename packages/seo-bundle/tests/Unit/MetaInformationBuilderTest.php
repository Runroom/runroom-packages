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
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Runroom\SeoBundle\MetaInformation\AbstractMetaInformationProvider;
use Runroom\SeoBundle\MetaInformation\MetaInformationBuilder;
use Runroom\SeoBundle\Repository\MetaInformationRepository;
use Runroom\SeoBundle\Tests\Fixtures\MetaInformationFixture;
use Runroom\SeoBundle\ViewModel\MetaInformationViewModel;
use Symfony\Component\PropertyAccess\PropertyAccess;

class MetaInformationBuilderTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<MetaInformationRepository> */
    private $repository;

    /** @var MetaInformationBuilder */
    private $builder;

    protected function setUp(): void
    {
        $this->repository = $this->prophesize(MetaInformationRepository::class);
        $this->repository->findOneBy(Argument::any())->willReturn(MetaInformationFixture::create());

        $this->builder = new MetaInformationBuilder(
            $this->repository->reveal(),
            PropertyAccess::createPropertyAccessor()
        );
    }

    /** @test */
    public function itBuildsMetaInformationViewModel(): void
    {
        $model = new \stdClass();
        $model->placeholder = 'test';

        $metas = $this->builder->build(new TestMetaInformationProvider(), 'test', $model);

        $this->assertInstanceOf(MetaInformationViewModel::class, $metas);
        $this->assertSame('test title', $metas->getTitle());
        $this->assertSame(' description', $metas->getDescription());
        $this->assertNull($metas->getImage());
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
