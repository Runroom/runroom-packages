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

use Psr\Container\ContainerInterface;
use Runroom\CookiesBundle\Controller\CookiesPageController;
use Runroom\CookiesBundle\Repository\CookiesPageRepository;
use Runroom\CookiesBundle\Service\CookiesPageService;
use Runroom\CookiesBundle\Twig\CookiesExtension;
use Runroom\CookiesBundle\Twig\CookiesRuntime;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "abstract_arg" function for creating references to arguments without value when dropping support for Symfony 4
    $services = $containerConfigurator->services();

    $services->set('runroom.cookies.controller.cookies_page', CookiesPageController::class)
        ->public()
        ->arg('$service', service('runroom.cookies.service.cookies_page'))
        ->call('setContainer', [service(ContainerInterface::class)])
        ->tag('container.service_subscriber')
        ->tag('controller.service_arguments');

    $services->set('runroom.cookies.service.cookies_page', CookiesPageService::class)
        ->arg('$repository', service(CookiesPageRepository::class))
        ->arg('$formFactory', service('form.factory'))
        ->arg('$cookies', null);

    $services->set(CookiesPageRepository::class)
        ->arg('$registry', service('doctrine'))
        ->tag('doctrine.repository_service');

    $services->set('runroom.cookies.twig.cookies', CookiesExtension::class)
        ->tag('twig.extension');

    $services->set('runroom.cookies.twig.cookies_runtime', CookiesRuntime::class)
        ->arg('$cookies', null)
        ->tag('twig.runtime');
};
