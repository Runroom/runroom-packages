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
use Runroom\UserBundle\Controller\SecurityController;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4
    $services = $containerConfigurator->services();

    $services->set('runroom_user.controller.security', SecurityController::class)
        ->public()
        ->arg('$authenticationUtils', new ReferenceConfigurator('security.authentication_utils'))
        ->tag('container.service_subscriber')
        ->call('setContainer', [new ReferenceConfigurator(ContainerInterface::class)]);
};
