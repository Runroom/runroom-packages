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
use Runroom\CkeditorSonataMediaBundle\Action\BrowserAction;
use Runroom\CkeditorSonataMediaBundle\Action\UploadAction;
use Runroom\CkeditorSonataMediaBundle\Admin\MediaAdminExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4
    $services = $containerConfigurator->services();

    $services->set('runroom.ckeditor_sonata_media.action.browser', BrowserAction::class)
        ->public()
        ->tag('container.service_subscriber')
        ->tag('controller.service_arguments')
        ->arg('$twig', new ReferenceConfigurator('twig'))
        ->arg('$adminFetcher', new ReferenceConfigurator('sonata.admin.request.fetcher'))
        ->arg('$mediaPool', new ReferenceConfigurator('sonata.media.pool'))
        ->call('setContainer', [new ReferenceConfigurator(ContainerInterface::class)]);

    $services->set('runroom.ckeditor_sonata_media.action.upload', UploadAction::class)
        ->public()
        ->tag('container.service_subscriber')
        ->tag('controller.service_arguments')
        ->arg('$adminFetcher', new ReferenceConfigurator('sonata.admin.request.fetcher'))
        ->arg('$mediaManager', new ReferenceConfigurator('sonata.media.manager.media'))
        ->arg('$mediaPool', new ReferenceConfigurator('sonata.media.pool'))
        ->call('setContainer', [new ReferenceConfigurator(ContainerInterface::class)]);

    $services->set('runroom.ckeditor_sonata_media.admin.media_admin', MediaAdminExtension::class)
        ->tag('sonata.admin.extension', ['target' => 'sonata.media.admin.media']);
};
