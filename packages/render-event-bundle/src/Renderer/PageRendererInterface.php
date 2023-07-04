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

use Symfony\Component\HttpFoundation\Response;

interface PageRendererInterface
{
    public function render(string $view, mixed $model = null): string;

    public function renderResponse(string $view, mixed $model = null, ?Response $response = null): Response;
}
