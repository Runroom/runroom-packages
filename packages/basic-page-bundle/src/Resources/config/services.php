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
use Runroom\RenderEventBundle\Renderer\PageRenderer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(BasicPageAdmin::class)
        ->public()
        ->args([null, BasicPage::class, null])
        ->tag('sonata.admin', ['manager_type' => 'orm', 'label' => 'Basic pages']);

    $services->set(BasicPageController::class)
        ->arg('$service', ref(BasicPageService::class))
        ->arg('$renderer', ref(PageRenderer::class))
        ->tag('controller.service_arguments');

    $services->set(BasicPageService::class)
        ->arg('$repository', ref(BasicPageRepository::class))
        ->tag('kernel.event_subscriber');

    $services->set(BasicPageAlternateLinksProvider::class)
        ->tag('runroom.seo.alternate_links');

    $services->set(BasicPageMetaInformationProvider::class)
        ->tag('runroom.seo.meta_information');

    $services->set(BasicPageRepository::class)
        ->arg('$registry', ref('doctrine'))
        ->arg('$requestStack', ref('request_stack'))
        ->tag('doctrine.repository_service');
};
