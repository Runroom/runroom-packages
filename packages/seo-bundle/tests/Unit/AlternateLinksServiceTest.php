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
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
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
    use ProphecyTrait;

    private const ROUTE = 'route';

    /** @var RequestStack */
    private $requestStack;

    /** @var ObjectProphecy<AbstractAlternateLinksProvider> */
    private $provider;

    /** @var DefaultAlternateLinksProvider */
    private $defaultProvider;

    /** @var ObjectProphecy<AlternateLinksBuilder> */
    private $builder;

    /** @var AlternateLinksService */
    private $service;

    protected function setUp(): void
    {
        $this->requestStack = new RequestStack();
        $this->provider = $this->prophesize(AbstractAlternateLinksProvider::class);
        $this->defaultProvider = new DefaultAlternateLinksProvider();
        $this->builder = $this->prophesize(AlternateLinksBuilder::class);

        $this->service = new AlternateLinksService(
            $this->requestStack,
            [$this->provider->reveal()],
            $this->defaultProvider,
            $this->builder->reveal()
        );
    }

    /**
     * @test
     */
    public function itFindsAlternateLinksForRoute(): void
    {
        $this->configureCurrentRequest();

        $this->provider->providesAlternateLinks(self::ROUTE)->willReturn(true);
        $this->builder->build($this->provider->reveal(), 'model', self::ROUTE, [])->willReturn(['alternate_links']);

        $event = $this->configurePageRenderEvent();
        $this->service->onPageRender($event);

        $this->assertSame(['alternate_links'], $event->getPageViewModel()->getContext('alternate_links'));
    }

    /**
     * @test
     */
    public function itFindsAlternateLinksForRouteWithTheDefaultProvider(): void
    {
        $this->configureCurrentRequest();

        $this->provider->providesAlternateLinks(self::ROUTE)->willReturn(false);
        $this->builder->build($this->defaultProvider, 'model', self::ROUTE, [])->willReturn(['alternate_links']);

        $event = $this->configurePageRenderEvent();
        $this->service->onPageRender($event);

        $this->assertSame(['alternate_links'], $event->getPageViewModel()->getContext('alternate_links'));
    }

    /**
     * @test
     */
    public function itHasSubscribedEvents(): void
    {
        $events = $this->service->getSubscribedEvents();
        $this->assertNotNull($events);
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

        $request->attributes->set('_route', self::ROUTE);
    }
}
