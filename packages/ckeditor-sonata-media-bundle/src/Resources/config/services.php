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
use Runroom\CkeditorSonataMediaBundle\Admin\MediaAdminExtension;
use Runroom\CkeditorSonataMediaBundle\Controller\MediaAdminController;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4
    $services = $containerConfigurator->services();

    $mediaAdminController = $services->set('runroom.ckeditor_sonata_media.controller.media_admin', MediaAdminController::class)
        ->public()
        ->arg('$mediaManager', new ReferenceConfigurator('sonata.media.manager.media'))
        ->arg('$mediaPool', new ReferenceConfigurator('sonata.media.pool'));

    /**
     * @todo: Simplify this when dropping support for SonataAdminBundle 3
     */
    if (is_a(CRUDController::class, AbstractController::class, true)) {
        $mediaAdminController
            ->call('setContainer', [new ReferenceConfigurator(ContainerInterface::class)])
            ->tag('container.service_subscriber')
            ->tag('controller.service_arguments');
    }

    $services->set('runroom.ckeditor_sonata_media.admin.media_admin', MediaAdminExtension::class)
        ->tag('sonata.admin.extension', ['target' => 'sonata.media.admin.media']);
};
