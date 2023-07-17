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

use Runroom\UserBundle\Twig\RolesMatrixExtension;
use Runroom\UserBundle\Twig\RolesMatrixRuntime;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('runroom.user.twig.extension.roles_matrix', RolesMatrixExtension::class)
        ->tag('twig.extension');

    $services->set('runroom.user.twig.runtime.roles_matrix', RolesMatrixRuntime::class)
        ->arg('$twig', service('twig'))
        ->arg('$rolesBuilder', service('runroom.user.security.roles_builder.matrix'))
        ->tag('twig.runtime');
};
