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

use Runroom\UserBundle\Security\RolesBuilder\AdminRolesBuilder;
use Runroom\UserBundle\Security\RolesBuilder\MatrixRolesBuilder;
use Runroom\UserBundle\Security\RolesBuilder\SecurityRolesBuilder;
use Runroom\UserBundle\Security\UserAuthenticator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4
    // Use "param" function for creating references to parameters when dropping support for Symfony 5.1
    $services = $containerConfigurator->services();

    $services->set('runroom_user.security.user_authenticator', UserAuthenticator::class)
        ->arg('$urlGenerator', new ReferenceConfigurator('router'));

    $services->set('runroom_user.security.roles_builder.admin', AdminRolesBuilder::class)
        ->arg('$authorizationChecker', new ReferenceConfigurator('security.authorization_checker'))
        ->arg('$pool', new ReferenceConfigurator('sonata.admin.pool'))
        ->arg('$configuration', new ReferenceConfigurator('sonata.admin.configuration'))
        ->arg('$translator', new ReferenceConfigurator('translator'));

    $services->set('runroom_user.security.roles_builder.matrix', MatrixRolesBuilder::class)
        ->arg('$tokenStorage', new ReferenceConfigurator('security.token_storage'))
        ->arg('$adminRolesBuilder', new ReferenceConfigurator('runroom_user.security.roles_builder.admin'))
        ->arg('$securityRolesBuilder', new ReferenceConfigurator('runroom_user.security.roles_builder.security'));

    $services->set('runroom_user.security.roles_builder.security', SecurityRolesBuilder::class)
        ->arg('$authorizationChecker', new ReferenceConfigurator('security.authorization_checker'))
        ->arg('$configuration', new ReferenceConfigurator('sonata.admin.configuration'))
        ->arg('$translator', new ReferenceConfigurator('translator'))
        ->arg('$rolesHierarchy', '%security.role_hierarchy.roles%');
};
