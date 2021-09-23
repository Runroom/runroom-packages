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

use Runroom\UserBundle\Admin\UserAdmin;
use Runroom\UserBundle\Entity\User;
use Runroom\UserBundle\Twig\GlobalVariables;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4
    $services = $containerConfigurator->services();

    $services->set('runroom_user.admin.user', UserAdmin::class)
        ->public()
        ->args([null, User::class, null])
        ->tag('sonata.admin', ['manager_type' => 'orm', 'label' => 'User'])
        ->call('setPasswordHasher', [new ReferenceConfigurator('security.password_hasher')]);

    $services->set('runroom_user.twig.global_variables', GlobalVariables::class)
        ->arg('$pool', new ReferenceConfigurator('sonata.admin.pool'));
};
