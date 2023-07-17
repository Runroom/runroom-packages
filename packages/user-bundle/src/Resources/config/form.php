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

use Runroom\UserBundle\Form\RolesMatrixType;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('runroom.user.form.type.roles_matrix', RolesMatrixType::class)
        ->arg('$rolesBuilder', service('runroom.user.security.roles_builder.matrix'))
        ->tag('form.type');
};
