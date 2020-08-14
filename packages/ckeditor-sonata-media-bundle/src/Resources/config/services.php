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
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(MediaAdminController::class)
        ->public()
        ->arg('$mediaManager', ref('sonata.media.manager.media'))
        ->arg('$mediaPool', ref('sonata.media.pool'))
        ->arg('$twig', ref('twig'));

    $services->set(MediaAdminExtension::class)
        ->tag('sonata.admin.extension', ['target' => 'sonata.media.admin.media']);
};
