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
use Runroom\SeoBundle\AlternateLinks\AbstractAlternateLinksProvider;
use Runroom\SeoBundle\AlternateLinks\AlternateLinksBuilder;
use Runroom\SeoBundle\AlternateLinks\AlternateLinksService;
use Runroom\SeoBundle\AlternateLinks\DefaultAlternateLinksProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class AlternateLinksServiceTest extends TestCase
{
    /** @var RequestStack */
    private $requestStack;

    /** @var MockObject&AbstractAlternateLinksProvider */
    private $provider;

    /** @var DefaultAlternateLinksProvider */
    private $defaultProvider;

    /** @var MockObject&AlternateLinksBuilder */
    private $builder;

    /** @var AlternateLinksService */
    private $service;

    protected function setUp(): void
    {
        $this->requestStack = new RequestStack();
        $this->provider = $this->createMock(AbstractAlternateLinksProvider::class);
        $this->defaultProvider = new DefaultAlternateLinksProvider();
        $this->builder = $this->createMock(AlternateLinksBuilder::class);

        $this->service = new AlternateLinksService(
            $this->requestStack,
            [$this->provider],
            $this->defaultProvider,
            $this->builder
        );
    }

    /** @test */
    public function itFindsAlternateLinksForRoute(): void
    {
        $this->configureCurrentRequest();

        $this->provider->method('providesAlternateLinks')->with('route')->willReturn(true);
        $this->builder->method('build')->with($this->provider, 'model', 'route', [])->willReturn(['alternate_links']);

        $event = $this->configurePageRenderEvent();
        $this->service->onPageRender($event);

        self::assertSame(['alternate_links'], $event->getPageViewModel()->getContext('alternate_links'));
    }

    /** @test */
    public function itFindsAlternateLinksForRouteWithTheDefaultProvider(): void
    {
        $this->configureCurrentRequest();

        $this->provider->method('providesAlternateLinks')->with('route')->willReturn(false);
        $this->builder->method('build')->with($this->defaultProvider, 'model', 'route', [])->willReturn(['alternate_links']);

        $event = $this->configurePageRenderEvent();
        $this->service->onPageRender($event);

        self::assertSame(['alternate_links'], $event->getPageViewModel()->getContext('alternate_links'));
    }

    /** @test */
    public function itHasSubscribedEvents(): void
    {
        $events = AlternateLinksService::getSubscribedEvents();

        self::assertCount(1, $events);
    }

    protected function configurePageRenderEvent(): PageRenderEvent
    {
        $response = new Response();
        $page = new PageViewModel();
        $page->setContent('model');

        return new PageRenderEvent('view', $page, $response);
    }

    protected function configureCurrentRequest(): void
    {
        $request = new Request();

        $this->requestStack->push($request);

        $request->attributes->set('_route', 'route');
    }
}
