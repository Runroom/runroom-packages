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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Runroom\SeoBundle\MetaInformation\DefaultMetaInformationProvider;
use Runroom\SeoBundle\MetaInformation\MetaInformationBuilder;
use Runroom\SeoBundle\MetaInformation\MetaInformationProviderInterface;
use Runroom\SeoBundle\MetaInformation\MetaInformationService;
use Runroom\SeoBundle\Model\SeoModelInterface;
use Runroom\SeoBundle\Tests\App\ViewModel\DummyViewModel;
use Runroom\SeoBundle\ViewModel\MetaInformationViewModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class MetaInformationServiceTest extends TestCase
{
    private RequestStack $requestStack;

    /** @phpstan-var MockObject&MetaInformationProviderInterface<SeoModelInterface> */
    private $provider;

    private DefaultMetaInformationProvider $defaultProvider;

    /** @var MockObject&MetaInformationBuilder */
    private $builder;

    private MetaInformationService $service;
    private DummyViewModel $model;

    private MetaInformationViewModel $expectedMetas;

    protected function setUp(): void
    {
        $this->requestStack = new RequestStack();
        $this->provider = $this->createMock(MetaInformationProviderInterface::class);
        $this->defaultProvider = new DefaultMetaInformationProvider();
        $this->builder = $this->createMock(MetaInformationBuilder::class);

        $this->service = new MetaInformationService(
            $this->requestStack,
            $this->getProviders(),
            $this->builder
        );

        $this->model = new DummyViewModel();
        $this->expectedMetas = new MetaInformationViewModel();
    }

    /** @test */
    public function itFindsMetasForRoute(): void
    {
        $this->configureCurrentRequest();
        $this->provider->method('providesMetas')->with('route')->willReturn(true);
        $this->builder->method('build')->with($this->provider, $this->model, 'route')
            ->willReturn($this->expectedMetas);

        $generatedMetas = $this->service->build($this->model);

        self::assertSame($this->expectedMetas, $generatedMetas);
    }

    /** @test */
    public function itFindsMetasForRouteWithTheDefaultProvider(): void
    {
        $this->configureCurrentRequest();
        $this->provider->method('providesMetas')->with('route')->willReturn(false);
        $this->builder->method('build')->with($this->defaultProvider, $this->model, 'route')
            ->willReturn($this->expectedMetas);

        $generatedMetas = $this->service->build($this->model);

        self::assertSame($this->expectedMetas, $generatedMetas);
    }

    /** @test */
    public function itThrowsIfNoProviderIsFound(): void
    {
        $service = new MetaInformationService($this->requestStack, [], $this->builder);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('There is no provided selected to build meta information');

        $service->build($this->model);
    }

    private function configureCurrentRequest(): void
    {
        $request = new Request();

        $this->requestStack->push($request);

        $request->attributes->set('_route', 'route');
    }

    /** @return iterable<MetaInformationProviderInterface<SeoModelInterface>> */
    private function getProviders(): iterable
    {
        yield $this->provider;
        yield $this->defaultProvider;
    }
}
