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

use Runroom\CkeditorSonataMediaBundle\Admin\MediaAdminExtension;
use Runroom\CkeditorSonataMediaBundle\Controller\MediaAdminController;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4.4
    $services = $containerConfigurator->services();

    $services->set(MediaAdminController::class)
        ->public()
        ->arg('$mediaManager', new ReferenceConfigurator('sonata.media.manager.media'))
        ->arg('$mediaPool', new ReferenceConfigurator('sonata.media.pool'))
        ->arg('$twig', new ReferenceConfigurator('twig'));

    $services->set(MediaAdminExtension::class)
        ->tag('sonata.admin.extension', ['target' => 'sonata.media.admin.media']);
};
