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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Runroom\RenderEventBundle\Controller\TemplateController;
use Runroom\RenderEventBundle\ErrorRenderer\TwigErrorRenderer;
use Runroom\RenderEventBundle\Renderer\PageRenderer;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('runroom.render_event.controller.template', TemplateController::class)
        ->arg('$renderer', service('runroom.render_event.renderer.page'))
        ->tag('controller.service_arguments');

    $services->set('runroom.render_event.renderer.page', PageRenderer::class)
        ->arg('$twig', service('twig'))
        ->arg('$eventDispatcher', service('event_dispatcher'))
        ->arg('$pageViewModel', service('runroom.render_event.page_view_model'));

    $services->set('runroom.render_event.error_renderer.twig_error', TwigErrorRenderer::class)
        ->decorate('twig.error_renderer.html')
        ->arg('$twig', service('twig'))
        ->arg('$renderer', service('runroom.render_event.renderer.page'))
        ->arg('$fallbackErrorRenderer', service('error_handler.error_renderer.html'))
        ->arg(
            '$debug',
            inline_service(TwigErrorRenderer::class)->factory([TwigErrorRenderer::class, 'isDebug'])
                ->args([service('request_stack'), param('kernel.debug')])
        );

    $services->set('runroom.render_event.page_view_model');
};
