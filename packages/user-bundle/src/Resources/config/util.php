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

use Runroom\UserBundle\Util\UserManipulator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;
use Symfony\Component\Security\Http\Authentication\AuthenticatorManager;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4
    $services = $containerConfigurator->services();

    /** @todo: Simplify this when dropping support for Symfony 4 */
    $passwordHasherId = class_exists(AuthenticatorManager::class) ? 'security.password_hasher' : 'security.password_encoder';

    $services->set('runroom_user.util.user_manipulator', UserManipulator::class)
        ->arg('$userRepository', new ReferenceConfigurator('runroom_user.repository.user'))
        ->arg('$passwordHasher', new ReferenceConfigurator($passwordHasherId));
};
