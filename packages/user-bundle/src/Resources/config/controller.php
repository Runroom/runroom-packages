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
use Runroom\UserBundle\Controller\SecurityController;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('runroom.user.controller.security', SecurityController::class)
        ->public()
        ->arg('$authenticationUtils', service('security.authentication_utils'))
        ->tag('container.service_subscriber')
        ->call('setContainer', [service(ContainerInterface::class)]);
};
