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
use Runroom\CkeditorSonataMediaBundle\Action\BrowserAction;
use Runroom\CkeditorSonataMediaBundle\Action\UploadAction;
use Runroom\CkeditorSonataMediaBundle\Admin\MediaAdminExtension;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('runroom.ckeditor_sonata_media.action.browser', BrowserAction::class)
        ->public()
        ->tag('container.service_subscriber')
        ->tag('controller.service_arguments')
        ->arg('$twig', service('twig'))
        ->arg('$adminFetcher', service('sonata.admin.request.fetcher'))
        ->arg('$mediaPool', service('sonata.media.pool'))
        ->call('setContainer', [service(ContainerInterface::class)]);

    $services->set('runroom.ckeditor_sonata_media.action.upload', UploadAction::class)
        ->public()
        ->tag('container.service_subscriber')
        ->tag('controller.service_arguments')
        ->arg('$adminFetcher', service('sonata.admin.request.fetcher'))
        ->arg('$mediaManager', service('sonata.media.manager.media'))
        ->arg('$mediaPool', service('sonata.media.pool'))
        ->call('setContainer', [service(ContainerInterface::class)]);

    $services->set('runroom.ckeditor_sonata_media.admin.media_admin', MediaAdminExtension::class)
        ->tag('sonata.admin.extension', ['target' => 'sonata.media.admin.media']);
};
