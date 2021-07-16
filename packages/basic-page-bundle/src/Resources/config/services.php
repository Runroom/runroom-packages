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

use Runroom\BasicPageBundle\Admin\BasicPageAdmin;
use Runroom\BasicPageBundle\Controller\BasicPageController;
use Runroom\BasicPageBundle\Entity\BasicPage;
use Runroom\BasicPageBundle\Repository\BasicPageRepository;
use Runroom\BasicPageBundle\Service\BasicPageAlternateLinksProvider;
use Runroom\BasicPageBundle\Service\BasicPageMetaInformationProvider;
use Runroom\BasicPageBundle\Service\BasicPageService;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4.4
    $services = $containerConfigurator->services();

    $services->set(BasicPageAdmin::class)
        ->public()
        ->args([null, BasicPage::class, null])
        ->tag('sonata.admin', ['manager_type' => 'orm', 'label' => 'Basic pages']);

    $services->set(BasicPageController::class)
        ->arg('$service', new ReferenceConfigurator(BasicPageService::class))
        ->call('setContainer', [new ReferenceConfigurator('service_container')])
        ->tag('controller.service_subscriber');

    $services->set(BasicPageService::class)
        ->arg('$repository', new ReferenceConfigurator(BasicPageRepository::class));

    $services->set(BasicPageAlternateLinksProvider::class)
        ->tag('runroom.seo.alternate_links');

    $services->set(BasicPageMetaInformationProvider::class)
        ->tag('runroom.seo.meta_information');

    $services->set(BasicPageRepository::class)
        ->arg('$registry', new ReferenceConfigurator('doctrine'))
        ->arg('$requestStack', new ReferenceConfigurator('request_stack'))
        ->tag('doctrine.repository_service');
};
