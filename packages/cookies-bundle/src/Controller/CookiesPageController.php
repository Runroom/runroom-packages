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

namespace Runroom\CookiesBundle\Controller;

use Runroom\RenderEventBundle\Renderer\PageRenderer;
use Runroom\CookiesBundle\Service\CookiesPageService;
use Symfony\Component\HttpFoundation\Response;

class CookiesPageController
{
    /** @var PageRenderer  */
    protected $renderer;

    /** @var CookiesPageService  */
    protected $service;

    public function __construct(
        PageRenderer $renderer,
        CookiesPageService $service
    ) {
        $this->renderer = $renderer;
        $this->service = $service;
    }

    public function index(): Response
    {
        $viewModel = $this->service->getViewModel();

        return $this->renderer->renderResponse('pages/cookies.html.twig', $viewModel);
    }
}
