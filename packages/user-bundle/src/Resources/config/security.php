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

use Runroom\UserBundle\Security\RolesBuilder\AdminRolesBuilder;
use Runroom\UserBundle\Security\RolesBuilder\MatrixRolesBuilder;
use Runroom\UserBundle\Security\RolesBuilder\SecurityRolesBuilder;
use Runroom\UserBundle\Security\UserAuthenticator;
use Runroom\UserBundle\Security\UserProvider;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('runroom.user.provider.user', UserProvider::class)
        ->arg('$userRepository', service('runroom.user.repository.user'));

    $services->set('runroom.user.security.roles_builder.admin', AdminRolesBuilder::class)
        ->arg('$authorizationChecker', service('security.authorization_checker'))
        ->arg('$pool', service('sonata.admin.pool'))
        ->arg('$configuration', service('sonata.admin.configuration'))
        ->arg('$translator', service('translator'));

    $services->set('runroom.user.security.roles_builder.matrix', MatrixRolesBuilder::class)
        ->arg('$tokenStorage', service('security.token_storage'))
        ->arg('$adminRolesBuilder', service('runroom.user.security.roles_builder.admin'))
        ->arg('$securityRolesBuilder', service('runroom.user.security.roles_builder.security'));

    $services->set('runroom.user.security.roles_builder.security', SecurityRolesBuilder::class)
        ->arg('$authorizationChecker', service('security.authorization_checker'))
        ->arg('$configuration', service('sonata.admin.configuration'))
        ->arg('$translator', service('translator'))
        ->arg('$rolesHierarchy', param('security.role_hierarchy.roles'));

    $services->set('runroom.user.security.user_authenticator', UserAuthenticator::class)
        ->arg('$urlGenerator', service('router'));
};
