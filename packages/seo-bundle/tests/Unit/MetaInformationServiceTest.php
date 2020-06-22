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
    use ProphecyTrait;

    private const ROUTE = 'route';

    /** @var RequestStack */
    private $requestStack;

    /** @var ObjectProphecy<MetaInformationProviderInterface> */
    private $provider;

    /** @var DefaultMetaInformationProvider */
    private $defaultProvider;

    /** @var ObjectProphecy<MetaInformationBuilder> */
    private $builder;

    /** @var MetaInformationService */
    private $service;

    /** @var \stdClass */
    private $model;

    /** @var MetaInformationViewModel */
    private $expectedMetas;

    protected function setUp(): void
    {
        $this->requestStack = new RequestStack();
        $this->provider = $this->prophesize(MetaInformationProviderInterface::class);
        $this->defaultProvider = new DefaultMetaInformationProvider();
        $this->builder = $this->prophesize(MetaInformationBuilder::class);

        $this->provider->providesMetas(Argument::any())->willReturn(false);

        $this->service = new MetaInformationService(
            $this->requestStack,
            [$this->provider->reveal()],
            $this->defaultProvider,
            $this->builder->reveal()
        );

        $this->model = new \stdClass();
        $this->expectedMetas = new MetaInformationViewModel();
    }

    /** @test */
    public function itFindsMetasForRoute(): void
    {
        $this->configureCurrentRequest();
        $this->provider->providesMetas(self::ROUTE)->willReturn(true);
        $this->builder->build($this->provider->reveal(), self::ROUTE, $this->model)
            ->willReturn($this->expectedMetas);

        $event = $this->configurePageRenderEvent();
        $this->service->onPageRender($event);

        self::assertSame($this->expectedMetas, $event->getPageViewModel()->getContext('metas'));
    }

    /** @test */
    public function itFindsMetasForRouteWithTheDefaultProvider(): void
    {
        $this->configureCurrentRequest();
        $this->builder->build($this->defaultProvider, self::ROUTE, $this->model)
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
        $response = $this->prophesize(Response::class);
        $page = new PageViewModel();
        $page->setContent($this->model);

        return new PageRenderEvent('view', $page, $response->reveal());
    }

    protected function configureCurrentRequest(): void
    {
        $request = new Request();

        $this->requestStack->push($request);

        $request->attributes->set('_route', self::ROUTE);
    }
}
