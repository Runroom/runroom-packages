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
use Runroom\SeoBundle\AlternateLinks\AlternateLinksBuilder;
use Runroom\SeoBundle\AlternateLinks\AlternateLinksProviderInterface;
use Runroom\SeoBundle\AlternateLinks\AlternateLinksService;
use Runroom\SeoBundle\AlternateLinks\DefaultAlternateLinksProvider;
use Runroom\SeoBundle\Model\SeoModelInterface;
use Runroom\SeoBundle\Tests\App\ViewModel\DummyViewModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class AlternateLinksServiceTest extends TestCase
{
    private RequestStack $requestStack;

    /** @phpstan-var MockObject&AlternateLinksProviderInterface<SeoModelInterface> */
    private $provider;

    private DefaultAlternateLinksProvider $defaultProvider;

    /** @var MockObject&AlternateLinksBuilder */
    private $builder;

    private AlternateLinksService $service;
    private DummyViewModel $model;

    protected function setUp(): void
    {
        $this->requestStack = new RequestStack();
        $this->provider = $this->createMock(AlternateLinksProviderInterface::class);
        $this->defaultProvider = new DefaultAlternateLinksProvider();
        $this->builder = $this->createMock(AlternateLinksBuilder::class);

        $this->service = new AlternateLinksService(
            $this->requestStack,
            $this->getProviders(),
            $this->builder
        );

        $this->model = new DummyViewModel();
    }

    /** @test */
    public function itFindsAlternateLinksForRoute(): void
    {
        $this->configureCurrentRequest();

        $this->provider->method('providesAlternateLinks')->with('route')->willReturn(true);
        $this->builder->method('build')->with($this->provider, $this->model, 'route', [])->willReturn(['es' => 'alternate_link']);

        $alternateLinks = $this->service->build($this->model);

        self::assertSame(['es' => 'alternate_link'], $alternateLinks);
    }

    /** @test */
    public function itFindsAlternateLinksForRouteWithTheDefaultProvider(): void
    {
        $this->configureCurrentRequest();

        $this->provider->method('providesAlternateLinks')->with('route')->willReturn(false);
        $this->builder->method('build')->with($this->defaultProvider, $this->model, 'route', [])->willReturn(['es' => 'alternate_link']);

        $alternateLinks = $this->service->build($this->model);

        self::assertSame(['es' => 'alternate_link'], $alternateLinks);
    }

    /** @test */
    public function itThrowsIfNoProviderIsFound(): void
    {
        $service = new AlternateLinksService($this->requestStack, [], $this->builder);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('There is no provided selected to build alternate links');

        $service->build($this->model);
    }

    private function configureCurrentRequest(): void
    {
        $request = new Request();

        $this->requestStack->push($request);

        $request->attributes->set('_route', 'route');
    }

    /** @return iterable<AlternateLinksProviderInterface<SeoModelInterface>> */
    private function getProviders(): iterable
    {
        yield $this->provider;
        yield $this->defaultProvider;
    }
}
