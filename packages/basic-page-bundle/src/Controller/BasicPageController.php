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

namespace Runroom\BasicPageBundle\Controller;

use Runroom\BasicPageBundle\Service\BasicPageService;
use Runroom\RenderEventBundle\Renderer\PageRenderer;
use Symfony\Component\HttpFoundation\Response;

final class BasicPageController
{
    protected $service;
    protected $renderer;

    public function __construct(
        PageRenderer $renderer,
        BasicPageService $service
    ) {
        $this->renderer = $renderer;
        $this->service = $service;
    }

    public function show(string $slug): Response
    {
        $model = $this->service->getBasicPageViewModel($slug);

        return $this->renderer->renderResponse('@RunroomBasicPage/show.html.twig', $model);
    }
}
