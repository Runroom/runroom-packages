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

use Runroom\UserBundle\Command\ActivateUserCommand;
use Runroom\UserBundle\Command\ChangePasswordCommand;
use Runroom\UserBundle\Command\CreateUserCommand;
use Runroom\UserBundle\Command\DeactivateUserCommand;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4.4
    $services = $containerConfigurator->services();

    $services->set('runroom_user.command.activate_user', ActivateUserCommand::class)
        ->tag('console.command')
        ->arg('$userManipulator', new ReferenceConfigurator('runroom_user.util.user_manipulator'));

    $services->set('runroom_user.command.change_password', ChangePasswordCommand::class)
        ->tag('console.command')
        ->arg('$userManipulator', new ReferenceConfigurator('runroom_user.util.user_manipulator'));

    $services->set('runroom_user.command.create_user', CreateUserCommand::class)
        ->tag('console.command')
        ->arg('$userManipulator', new ReferenceConfigurator('runroom_user.util.user_manipulator'));

    $services->set('runroom_user.command.deactivate_user', DeactivateUserCommand::class)
        ->tag('console.command')
        ->arg('$userManipulator', new ReferenceConfigurator('runroom_user.util.user_manipulator'));
};
