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

use Psr\Container\ContainerInterface;
use Runroom\CookiesBundle\Admin\CookiesPageAdmin;
use Runroom\CookiesBundle\Controller\CookiesPageController;
use Runroom\CookiesBundle\Entity\CookiesPage;
use Runroom\CookiesBundle\Repository\CookiesPageRepository;
use Runroom\CookiesBundle\Service\CookiesPageService;
use Runroom\CookiesBundle\Twig\CookiesExtension;
use Runroom\CookiesBundle\Twig\CookiesRuntime;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4.4
    // Use "abstract_arg" function for creating references to arguments without value when dropping support for Symfony 4.4
    $services = $containerConfigurator->services();

    $services->set(CookiesPageAdmin::class)
        ->public()
        ->args([null, CookiesPage::class, null])
        ->tag('sonata.admin', ['manager_type' => 'orm', 'label' => 'Cookies']);

    $services->set(CookiesPageController::class)
        ->public()
        ->arg('$service', new ReferenceConfigurator(CookiesPageService::class))
        ->call('setContainer', [new ReferenceConfigurator(ContainerInterface::class)])
        ->tag('container.service_subscriber')
        ->tag('controller.service_arguments');

    $services->set(CookiesPageService::class)
        ->arg('$repository', new ReferenceConfigurator(CookiesPageRepository::class))
        ->arg('$formFactory', new ReferenceConfigurator('form.factory'))
        ->arg('$cookies', null);

    $services->set(CookiesPageRepository::class)
        ->arg('$registry', new ReferenceConfigurator('doctrine'))
        ->tag('doctrine.repository_service');

    $services->set(CookiesExtension::class)
        ->tag('twig.extension');

    $services->set(CookiesRuntime::class)
        ->arg('$cookies', null)
        ->tag('twig.runtime');
};
