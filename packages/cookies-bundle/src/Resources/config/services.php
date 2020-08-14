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

use Runroom\CookiesBundle\Admin\CookiesPageAdmin;
use Runroom\CookiesBundle\Controller\CookiesPageController;
use Runroom\CookiesBundle\Entity\CookiesPage;
use Runroom\CookiesBundle\Repository\CookiesPageRepository;
use Runroom\CookiesBundle\Service\CookiesPageService;
use Runroom\CookiesBundle\Service\CookiesService;
use Runroom\FormHandlerBundle\FormHandler;
use Runroom\RenderEventBundle\Renderer\PageRenderer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(CookiesPageAdmin::class)
        ->public()
        ->args([null, CookiesPage::class, null])
        ->tag('sonata.admin', ['manager_type' => 'orm', 'label' => 'Cookies']);

    $services->set(CookiesPageController::class)
        ->arg('$service', ref(CookiesPageService::class))
        ->arg('$renderer', ref(PageRenderer::class))
        ->tag('controller.service_arguments');

    $services->set(CookiesPageService::class)
        ->arg('$repository', ref(CookiesPageRepository::class))
        ->arg('$handler', ref(FormHandler::class))
        ->arg('$cookies', null);

    $services->set(CookiesService::class)
        ->arg('$cookies', null)
        ->tag('kernel.event_subscriber');

    $services->set(CookiesPageRepository::class)
        ->arg('$registry', ref('doctrine'))
        ->tag('doctrine.repository_service');
};
