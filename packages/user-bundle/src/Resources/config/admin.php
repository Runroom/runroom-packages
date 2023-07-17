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

use Runroom\UserBundle\Admin\UserAdmin;
use Runroom\UserBundle\Entity\User;
use Runroom\UserBundle\Twig\GlobalVariables;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('runroom.user.admin.user', UserAdmin::class)
        ->public()
        ->arg('$passwordHasher', service('security.password_hasher'))
        ->tag('sonata.admin', [
            'model_class' => User::class,
            'manager_type' => 'orm',
            'label' => 'User',
        ]);

    $services->set('runroom.user.twig.global_variables', GlobalVariables::class)
        ->arg('$pool', service('sonata.admin.pool'))
        ->arg('$hasRequestPasswordEnabled', null);
};
