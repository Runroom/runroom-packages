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

use Runroom\RenderEventBundle\Renderer\PageRendererInterface;
use Symfony\Component\HttpFoundation\Response;

final class TemplateController
{
    public function __construct(private readonly PageRendererInterface $renderer) {}

    /**
     * @param array<string, mixed>|null $context
     */
    public function __invoke(string $template, ?array $context = null): Response
    {
        return $this->renderer->renderResponse($template, $context);
    }
}
