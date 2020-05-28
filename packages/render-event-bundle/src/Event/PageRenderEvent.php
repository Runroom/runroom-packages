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

namespace Runroom\RenderEventBundle\Event;

use Runroom\RenderEventBundle\ViewModel\PageViewModelInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

/** @final */
class PageRenderEvent extends Event
{
    public const EVENT_NAME = 'runroom.event.page.render';

    /** @var string */
    private $view;

    /** @var PageViewModelInterface */
    private $pageViewModel;

    /** @var Response|null */
    private $response;

    public function __construct(
        string $view,
        PageViewModelInterface $pageViewModel,
        ?Response $response = null
    ) {
        $this->view = $view;
        $this->pageViewModel = $pageViewModel;
        $this->response = $response;
    }

    public function setView(string $view): self
    {
        $this->view = $view;

        return $this;
    }

    public function getView(): string
    {
        return $this->view;
    }

    public function setPageViewModel(PageViewModelInterface $pageViewModel): self
    {
        $this->pageViewModel = $pageViewModel;

        return $this;
    }

    public function getPageViewModel(): PageViewModelInterface
    {
        return $this->pageViewModel;
    }

    public function setResponse(Response $response): self
    {
        $this->response = $response;

        return $this;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }
}
