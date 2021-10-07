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

use Psr\Container\ContainerInterface;
use Runroom\UserBundle\Controller\ResetPasswordController;
use Runroom\UserBundle\Entity\ResetPasswordRequest;
use Runroom\UserBundle\Form\ChangePasswordFormType;
use Runroom\UserBundle\Form\ResetPasswordRequestFormType;
use Runroom\UserBundle\Repository\ResetPasswordRequestRepository;
use Runroom\UserBundle\Service\MailerService;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;
use Symfony\Component\Security\Http\Authentication\AuthenticatorManager;
use SymfonyCasts\Bundle\ResetPassword\Command\ResetPasswordRemoveExpiredCommand;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelper;
use SymfonyCasts\Bundle\ResetPassword\Util\ResetPasswordCleaner;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4
    // Use "abstract_arg" function for referencing parameters that will be replaced when dropping support for Symfony 4
    $services = $containerConfigurator->services();

    /** @todo: Simplify this when dropping support for Symfony 4 */
    $passwordHasherId = class_exists(AuthenticatorManager::class) ? 'security.password_hasher' : 'security.password_encoder';

    $services->set('runroom_user.command.reset_password_remove_expired', ResetPasswordRemoveExpiredCommand::class)
        ->arg('$cleaner', new ReferenceConfigurator('runroom_user.reset_password.cleaner'))
        ->tag('console.command', ['command' => 'runroom:user:remove-expired-password-request']);

    $services->set('runroom_user.controller.reset_password', ResetPasswordController::class)
        ->public()
        ->arg('$resetPasswordHelper', new ReferenceConfigurator('runroom_user.reset_password.helper'))
        ->arg('$passwordHasher', new ReferenceConfigurator($passwordHasherId))
        ->arg('$mailerService', new ReferenceConfigurator('runroom_user.service.mailer'))
        ->arg('$userProvider', new ReferenceConfigurator('runroom_user.provider.user'))
        ->tag('container.service_subscriber')
        ->call('setContainer', [new ReferenceConfigurator(ContainerInterface::class)]);

    $services->set('runroom_user.form.type.change_password', ChangePasswordFormType::class)
        ->tag('form.type');

    $services->set('runroom_user.form.type.reset_password_request', ResetPasswordRequestFormType::class)
        ->tag('form.type');

    $services->set('runroom_user.service.mailer', MailerService::class)
        ->arg('$mailer', new ReferenceConfigurator('mailer'))
        ->arg('$translator', new ReferenceConfigurator('translator'))
        ->arg('$twig', new ReferenceConfigurator('twig'))
        ->arg('$fromEmail', null)
        ->arg('$fromName', null);

    $services->set('runroom_user.repository.reset_password_request', ResetPasswordRequestRepository::class)
        ->arg('$entityManager', new ReferenceConfigurator('doctrine.orm.entity_manager'))
        ->arg('$class', ResetPasswordRequest::class);

    // Services from SymfonyCasts Reset Password Bundle
    $services->set('runroom_user.reset_password.cleaner', ResetPasswordCleaner::class)
        ->arg('$repository', new ReferenceConfigurator('runroom_user.repository.reset_password_request'))
        ->arg('$enabled', null);

    $services->set('runroom_user.reset_password.helper', ResetPasswordHelper::class)
        ->arg('$generator', new ReferenceConfigurator('symfonycasts.reset_password.token_generator'))
        ->arg('$cleaner', new ReferenceConfigurator('runroom_user.reset_password.cleaner'))
        ->arg('$repository', new ReferenceConfigurator('runroom_user.repository.reset_password_request'))
        ->arg('$resetRequestLifetime', null)
        ->arg('$requestThrottleTime', null);
};
