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

use Runroom\UserBundle\Command\ActivateUserCommand;
use Runroom\UserBundle\Command\ChangePasswordCommand;
use Runroom\UserBundle\Command\CreateUserCommand;
use Runroom\UserBundle\Command\DeactivateUserCommand;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('runroom.user.command.activate_user', ActivateUserCommand::class)
        ->tag('console.command')
        ->arg('$userManipulator', service('runroom.user.util.user_manipulator'));

    $services->set('runroom.user.command.change_password', ChangePasswordCommand::class)
        ->tag('console.command')
        ->arg('$userManipulator', service('runroom.user.util.user_manipulator'));

    $services->set('runroom.user.command.create_user', CreateUserCommand::class)
        ->tag('console.command')
        ->arg('$userManipulator', service('runroom.user.util.user_manipulator'));

    $services->set('runroom.user.command.deactivate_user', DeactivateUserCommand::class)
        ->tag('console.command')
        ->arg('$userManipulator', service('runroom.user.util.user_manipulator'));
};
