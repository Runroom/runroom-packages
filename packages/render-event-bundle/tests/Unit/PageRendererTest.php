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

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Runroom\RenderEventBundle\Event\PageRenderEvent;
use Runroom\RenderEventBundle\Renderer\PageRenderer;
use Runroom\RenderEventBundle\ViewModel\PageViewModel;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;

class PageRendererTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<Environment> */
    private $twig;

    /** @var ObjectProphecy<EventDispatcherInterface> */
    private $eventDispatcher;

    /** @var ObjectProphecy<PageViewModel> */
    private $pageViewModel;

    /** @var PageRenderer */
    private $service;

    protected function setUp(): void
    {
        $this->twig = $this->prophesize(Environment::class);
        $this->eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $this->pageViewModel = $this->prophesize(PageViewModel::class);

        $this->service = new PageRenderer(
            $this->twig->reveal(),
            $this->eventDispatcher->reveal(),
            $this->pageViewModel->reveal()
        );
    }

    /** @test */
    public function itDispatchEventsOnRenderResponse(): void
    {
        $response = new Response();

        $this->pageViewModel->setContent([])->shouldBeCalled();
        $this->twig->render('test.html.twig', Argument::type('array'), null)
            ->willReturn('Rendered test');
        $this->eventDispatcher->dispatch(Argument::type(PageRenderEvent::class), PageRenderEvent::EVENT_NAME)->willReturnArgument(0);

        $resultResponse = $this->service->renderResponse('test.html.twig', [], $response);

        $this->assertSame($response, $resultResponse);
    }
}
