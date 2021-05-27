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
use Runroom\RenderEventBundle\Event\PageRenderEvent;
use Runroom\RenderEventBundle\ViewModel\PageViewModel;
use Runroom\SeoBundle\MetaInformation\DefaultMetaInformationProvider;
use Runroom\SeoBundle\MetaInformation\MetaInformationBuilder;
use Runroom\SeoBundle\MetaInformation\MetaInformationProviderInterface;
use Runroom\SeoBundle\MetaInformation\MetaInformationService;
use Runroom\SeoBundle\ViewModel\MetaInformationViewModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class MetaInformationServiceTest extends TestCase
{
    private RequestStack $requestStack;

    /** @var MockObject&MetaInformationProviderInterface */
    private $provider;

    private DefaultMetaInformationProvider $defaultProvider;

    /** @var MockObject&MetaInformationBuilder */
    private $builder;

    private MetaInformationService $service;
    private \stdClass $model;
    private MetaInformationViewModel $expectedMetas;

    protected function setUp(): void
    {
        $this->requestStack = new RequestStack();
        $this->provider = $this->createMock(MetaInformationProviderInterface::class);
        $this->defaultProvider = new DefaultMetaInformationProvider();
        $this->builder = $this->createMock(MetaInformationBuilder::class);

        $this->service = new MetaInformationService(
            $this->requestStack,
            [$this->provider],
            $this->defaultProvider,
            $this->builder
        );

        $this->model = new \stdClass();
        $this->expectedMetas = new MetaInformationViewModel();
    }

    /** @test */
    public function itFindsMetasForRoute(): void
    {
        $this->configureCurrentRequest();
        $this->provider->method('providesMetas')->with('route')->willReturn(true);
        $this->builder->method('build')->with($this->provider, 'route', $this->model)
            ->willReturn($this->expectedMetas);

        $event = $this->configurePageRenderEvent();
        $this->service->onPageRender($event);

        self::assertSame($this->expectedMetas, $event->getPageViewModel()->getContext('metas'));
    }

    /** @test */
    public function itFindsMetasForRouteWithTheDefaultProvider(): void
    {
        $this->configureCurrentRequest();
        $this->provider->method('providesMetas')->with('route')->willReturn(false);
        $this->builder->method('build')->with($this->defaultProvider, 'route', $this->model)
            ->willReturn($this->expectedMetas);

        $event = $this->configurePageRenderEvent();
        $this->service->onPageRender($event);

        self::assertSame($this->expectedMetas, $event->getPageViewModel()->getContext('metas'));
    }

    /** @test */
    public function itHasSubscribedEvents(): void
    {
        $events = MetaInformationService::getSubscribedEvents();

        self::assertCount(1, $events);
    }

    protected function configurePageRenderEvent(): PageRenderEvent
    {
        $response = $this->createStub(Response::class);
        $page = new PageViewModel();
        $page->setContent($this->model);

        return new PageRenderEvent('view', $page, $response);
    }

    protected function configureCurrentRequest(): void
    {
        $request = new Request();

        $this->requestStack->push($request);

        $request->attributes->set('_route', 'route');
    }
}
