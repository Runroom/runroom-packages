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
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\InlineServiceConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "inline_service" function for creating inline services when dropping support for Symfony 4.4
    // Use "service" function for creating references to services when dropping support for Symfony 4.4
    $services = $containerConfigurator->services();

    $services->set(TemplateController::class)
        ->arg('$renderer', new ReferenceConfigurator(PageRenderer::class))
        ->tag('controller.service_arguments');

    $services->set(PageRenderer::class)
        ->arg('$twig', new ReferenceConfigurator('twig'))
        ->arg('$eventDispatcher', new ReferenceConfigurator('event_dispatcher'))
        ->arg('$pageViewModel', new ReferenceConfigurator('runroom.render_event.page_view_model'));

    $services->set(TwigErrorRenderer::class)
        ->decorate('twig.error_renderer.html')
        ->arg('$twig', new ReferenceConfigurator('twig'))
        ->arg('$fallbackErrorRenderer', new ReferenceConfigurator('error_handler.error_renderer.html'))
        ->arg('$renderer', new ReferenceConfigurator(PageRenderer::class))
        ->arg('$debug', (new InlineServiceConfigurator(new Definition(TwigErrorRenderer::class)))
            ->factory([TwigErrorRenderer::class, 'isDebug'])
            ->args([new ReferenceConfigurator('request_stack'), '%kernel.debug%'])
        );

    $services->set('runroom.render_event.page_view_model');
};
