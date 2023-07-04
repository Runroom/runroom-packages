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
use Runroom\BasicPageBundle\Controller\BasicPageController;
use Runroom\BasicPageBundle\Repository\BasicPageRepository;
use Runroom\BasicPageBundle\Service\BasicPageAlternateLinksProvider;
use Runroom\BasicPageBundle\Service\BasicPageMetaInformationProvider;
use Runroom\BasicPageBundle\Service\BasicPageService;
use Runroom\BasicPageBundle\Twig\BasicPageExtension;
use Runroom\BasicPageBundle\Twig\BasicPageRuntime;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('runroom.basic_page.controller.basic_page', BasicPageController::class)
        ->public()
        ->arg('$service', service('runroom.basic_page.service.basic_page'))
        ->call('setContainer', [service(ContainerInterface::class)])
        ->tag('container.service_subscriber')
        ->tag('controller.service_arguments');

    $services->set('runroom.basic_page.service.basic_page', BasicPageService::class)
        ->arg('$repository', service(BasicPageRepository::class));

    $services->set('runroom.basic_page.service.basic_page_alternate_links', BasicPageAlternateLinksProvider::class)
        ->tag('runroom.seo.alternate_links');

    $services->set('runroom.basic_page.service.basic_page_meta_information', BasicPageMetaInformationProvider::class)
        ->tag('runroom.seo.meta_information');

    $services->set(BasicPageRepository::class)
        ->arg('$registry', service('doctrine'))
        ->arg('$requestStack', service('request_stack'))
        ->tag('doctrine.repository_service');

    $services->set('runroom.basic_page.twig.basic_page', BasicPageExtension::class)
        ->tag('twig.extension');

    $services->set('runroom.basic_page.twig.basic_page.runtime', BasicPageRuntime::class)
        ->arg('$repository', service(BasicPageRepository::class))
        ->tag('twig.runtime');
};
