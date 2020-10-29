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

namespace Runroom\RenderEventBundle\Tests\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Runroom\RenderEventBundle\Event\PageRenderEvent;
use Runroom\RenderEventBundle\Renderer\PageRenderer;
use Runroom\RenderEventBundle\ViewModel\PageViewModel;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class PageRendererTest extends TestCase
{
    /** @var MockObject&Environment */
    private $twig;

    /** @var EventDispatcher */
    private $eventDispatcher;

    /** @var PageViewModel */
    private $pageViewModel;

    /** @var PageRenderer */
    private $service;

    protected function setUp(): void
    {
        $this->twig = $this->createMock(Environment::class);
        $this->eventDispatcher = new EventDispatcher();
        $this->pageViewModel = new PageViewModel();

        $this->service = new PageRenderer(
            $this->twig,
            $this->eventDispatcher,
            $this->pageViewModel
        );
    }

    /** @test */
    public function itDispatchEventsOnRender(): void
    {
        $this->twig->method('render')->with('test.html.twig', self::isType('array'))
            ->willReturn('Rendered test');

        $result = $this->service->render('test.html.twig', []);

        self::assertSame('Rendered test', $result);
    }

    /** @test */
    public function itDispatchEventsOnRenderResponse(): void
    {
        $response = new Response();

        $this->twig->method('render')->with('different.html.twig', self::isType('array'))
            ->willReturn('Rendered test');

        $this->eventDispatcher->addListener(PageRenderEvent::EVENT_NAME, function (PageRenderEvent $event): void {
            $pageViewModel = $event->getPageViewModel();
            $pageViewModel->addContext('test', 'test');

            $event->setPageViewModel($pageViewModel);
            $event->setView('different.html.twig');
        });

        $resultResponse = $this->service->renderResponse('test.html.twig', [], $response);

        self::assertSame($response, $resultResponse);
    }

    /** @test */
    public function itReturnsRedirectResponse(): void
    {
        $response = new Response();

        $this->twig->method('render')->with('test.html.twig', self::isType('array'), null)
            ->willReturn('Rendered test');

        $this->eventDispatcher->addListener(PageRenderEvent::EVENT_NAME, function (PageRenderEvent $event): void {
            $event->setResponse(new RedirectResponse('https://localhost'));
        });

        $resultResponse = $this->service->renderResponse('test.html.twig', [], $response);

        self::assertInstanceOf(RedirectResponse::class, $resultResponse);
    }
}
