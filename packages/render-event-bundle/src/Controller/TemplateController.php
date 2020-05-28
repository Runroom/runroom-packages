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

namespace Runroom\RenderEventBundle\Controller;

use Runroom\RenderEventBundle\Renderer\PageRenderer;
use Symfony\Component\HttpFoundation\Response;

final class TemplateController
{
    /** @var PageRenderer */
    private $renderer;

    public function __construct(PageRenderer $renderer = null)
    {
        $this->renderer = $renderer;
    }

    public function __invoke(string $template, array $context = []): Response
    {
        return $this->renderer->renderResponse($template, $context);
    }
}
