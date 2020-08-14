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

use Runroom\RenderEventBundle\Controller\TemplateController;
use Runroom\RenderEventBundle\ErrorRenderer\TwigErrorRenderer;
use Runroom\RenderEventBundle\Renderer\PageRenderer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\inline;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults();

    $services->set(TemplateController::class)
        ->arg('$renderer', ref(PageRenderer::class))
        ->tag('controller.service_arguments');

    $services->set(PageRenderer::class)
        ->arg('$twig', ref('twig'))
        ->arg('$eventDispatcher', ref('event_dispatcher'))
        ->arg('$pageViewModel', ref('runroom.render_event.page_view_model'));

    $services->set(TwigErrorRenderer::class)
        ->decorate('twig.error_renderer.html')
        ->arg('$twig', ref('twig'))
        ->arg('$fallbackErrorRenderer', ref('error_handler.error_renderer.html'))
        ->arg('$debug', inline('bool')->factory([inline(TwigErrorRenderer::class), 'isDebug']))
        ->arg('$renderer', ref(PageRenderer::class));

    $services->set('runroom.render_event.page_view_model');
};
