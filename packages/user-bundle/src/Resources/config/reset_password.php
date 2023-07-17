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

use Psr\Container\ContainerInterface;
use Runroom\UserBundle\Controller\ResetPasswordController;
use Runroom\UserBundle\Entity\ResetPasswordRequest;
use Runroom\UserBundle\Form\ChangePasswordFormType;
use Runroom\UserBundle\Form\ResetPasswordRequestFormType;
use Runroom\UserBundle\Repository\ResetPasswordRequestRepository;
use Runroom\UserBundle\Service\MailerService;
use SymfonyCasts\Bundle\ResetPassword\Command\ResetPasswordRemoveExpiredCommand;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelper;
use SymfonyCasts\Bundle\ResetPassword\Util\ResetPasswordCleaner;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "abstract_arg" function for referencing parameters that will be replaced when dropping support for Symfony 4
    $services = $containerConfigurator->services();

    $services->set('runroom.user.command.reset_password_remove_expired', ResetPasswordRemoveExpiredCommand::class)
        ->arg('$cleaner', service('runroom.user.reset_password.cleaner'))
        ->tag('console.command', ['command' => 'runroom:user:remove-expired-password-request']);

    $services->set('runroom.user.controller.reset_password', ResetPasswordController::class)
        ->public()
        ->arg('$resetPasswordHelper', service('runroom.user.reset_password.helper'))
        ->arg('$passwordHasher', service('security.password_hasher'))
        ->arg('$mailerService', service('runroom.user.service.mailer'))
        ->arg('$userProvider', service('runroom.user.provider.user'))
        ->tag('container.service_subscriber')
        ->call('setContainer', [service(ContainerInterface::class)]);

    $services->set('runroom.user.form.type.change_password', ChangePasswordFormType::class)
        ->tag('form.type');

    $services->set('runroom.user.form.type.reset_password_request', ResetPasswordRequestFormType::class)
        ->tag('form.type');

    $services->set('runroom.user.service.mailer', MailerService::class)
        ->arg('$mailer', service('mailer'))
        ->arg('$translator', service('translator'))
        ->arg('$twig', service('twig'))
        ->arg('$fromEmail', null)
        ->arg('$fromName', null);

    $services->set('runroom.user.repository.reset_password_request', ResetPasswordRequestRepository::class)
        ->arg('$entityManager', service('doctrine.orm.entity_manager'))
        ->arg('$class', ResetPasswordRequest::class);

    // Services from SymfonyCasts Reset Password Bundle
    $services->set('runroom.user.reset_password.cleaner', ResetPasswordCleaner::class)
        ->arg('$repository', service('runroom.user.repository.reset_password_request'))
        ->arg('$enabled', null);

    $services->set('runroom.user.reset_password.helper', ResetPasswordHelper::class)
        ->arg('$generator', service('symfonycasts.reset_password.token_generator'))
        ->arg('$cleaner', service('runroom.user.reset_password.cleaner'))
        ->arg('$repository', service('runroom.user.repository.reset_password_request'))
        ->arg('$resetRequestLifetime', null)
        ->arg('$requestThrottleTime', null);
};
