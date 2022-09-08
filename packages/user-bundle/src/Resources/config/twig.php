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

use Runroom\UserBundle\Twig\RolesMatrixExtension;
use Runroom\UserBundle\Twig\RolesMatrixRuntime;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4
    $services = $containerConfigurator->services();

    $services->set('runroom.user.twig.extension.roles_matrix', RolesMatrixExtension::class)
        ->tag('twig.extension');

    $services->set('runroom.user.twig.runtime.roles_matrix', RolesMatrixRuntime::class)
        ->arg('$twig', new ReferenceConfigurator('twig'))
        ->arg('$rolesBuilder', new ReferenceConfigurator('runroom.user.security.roles_builder.matrix'))
        ->tag('twig.runtime');
};
