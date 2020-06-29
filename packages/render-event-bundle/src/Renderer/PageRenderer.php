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

namespace Runroom\RenderEventBundle\Renderer;

use Runroom\RenderEventBundle\Event\PageRenderEvent;
use Runroom\RenderEventBundle\ViewModel\PageViewModelInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;

/** @final */
class PageRenderer
{
    /** @var Environment */
    private $twig;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var PageViewModelInterface */
    private $pageViewModel;

    public function __construct(
        Environment $twig,
        EventDispatcherInterface $eventDispatcher,
        PageViewModelInterface $pageViewModel
    ) {
        $this->twig = $twig;
        $this->eventDispatcher = $eventDispatcher;
        $this->pageViewModel = $pageViewModel;
    }

    /** @param mixed $model */
    public function render(string $view, $model = null): string
    {
        $this->pageViewModel->setContent($model);

        /** @var PageRenderEvent */
        $event = $this->eventDispatcher->dispatch(
            new PageRenderEvent($view, $this->pageViewModel),
            PageRenderEvent::EVENT_NAME
        );

        return $this->twig->render($event->getView(), ['page' => $event->getPageViewModel()]);
    }

    /** @param mixed $model */
    public function renderResponse(string $view, $model = null, Response $response = null): Response
    {
        $this->pageViewModel->setContent($model);

        /** @var PageRenderEvent */
        $event = $this->eventDispatcher->dispatch(
            new PageRenderEvent($view, $this->pageViewModel, $response),
            PageRenderEvent::EVENT_NAME
        );

        $response = $event->getResponse() ?? new Response();
        if ($response instanceof RedirectResponse || '' !== $response->getContent()) {
            return $response;
        }

        return $response->setContent($this->twig->render(
            $event->getView(),
            ['page' => $event->getPageViewModel()]
        ));
    }
}
