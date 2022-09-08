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

namespace Runroom\UserBundle\Tests\Integration;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Runroom\UserBundle\Admin\ResetPasswordRequestAdmin;
use Runroom\UserBundle\Admin\UserAdmin;
use Runroom\UserBundle\Command\ActivateUserCommand;
use Runroom\UserBundle\Command\ChangePasswordCommand;
use Runroom\UserBundle\Command\CreateUserCommand;
use Runroom\UserBundle\Command\DeactivateUserCommand;
use Runroom\UserBundle\Controller\ResetPasswordController;
use Runroom\UserBundle\Controller\SecurityController;
use Runroom\UserBundle\DependencyInjection\RunroomUserExtension;
use Runroom\UserBundle\Form\ChangePasswordFormType;
use Runroom\UserBundle\Form\ResetPasswordRequestFormType;
use Runroom\UserBundle\Form\RolesMatrixType;
use Runroom\UserBundle\Repository\ResetPasswordRequestRepository;
use Runroom\UserBundle\Repository\UserRepository;
use Runroom\UserBundle\Security\RolesBuilder\AdminRolesBuilder;
use Runroom\UserBundle\Security\RolesBuilder\MatrixRolesBuilder;
use Runroom\UserBundle\Security\RolesBuilder\SecurityRolesBuilder;
use Runroom\UserBundle\Security\UserAuthenticator;
use Runroom\UserBundle\Service\MailerService;
use Runroom\UserBundle\Twig\GlobalVariables;
use Runroom\UserBundle\Twig\RolesMatrixExtension;
use Runroom\UserBundle\Twig\RolesMatrixRuntime;
use Runroom\UserBundle\Util\UserManipulator;
use Symfony\Component\Security\Http\Authentication\AuthenticatorManager;
use SymfonyCasts\Bundle\ResetPassword\Command\ResetPasswordRemoveExpiredCommand;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelper;
use SymfonyCasts\Bundle\ResetPassword\Util\ResetPasswordCleaner;

class RunroomUserExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @test
     */
    public function itHasCoreServicesAlias(): void
    {
        $this->container->setParameter('kernel.bundles', []);
        $this->load([]);

        $this->assertContainerBuilderHasService('runroom.user.command.activate_user', ActivateUserCommand::class);
        $this->assertContainerBuilderHasService('runroom.user.command.change_password', ChangePasswordCommand::class);
        $this->assertContainerBuilderHasService('runroom.user.command.create_user', CreateUserCommand::class);
        $this->assertContainerBuilderHasService('runroom.user.command.deactivate_user', DeactivateUserCommand::class);
        $this->assertContainerBuilderHasService('runroom.user.controller.security', SecurityController::class);
        $this->assertContainerBuilderHasService('runroom.user.form.type.roles_matrix', RolesMatrixType::class);
        $this->assertContainerBuilderHasService('runroom.user.repository.user', UserRepository::class);
        $this->assertContainerBuilderHasService('runroom.user.security.roles_builder.admin', AdminRolesBuilder::class);
        $this->assertContainerBuilderHasService('runroom.user.security.roles_builder.matrix', MatrixRolesBuilder::class);
        $this->assertContainerBuilderHasService('runroom.user.security.roles_builder.security', SecurityRolesBuilder::class);
        $this->assertContainerBuilderHasService('runroom.user.twig.extension.roles_matrix', RolesMatrixExtension::class);
        $this->assertContainerBuilderHasService('runroom.user.twig.runtime.roles_matrix', RolesMatrixRuntime::class);
        $this->assertContainerBuilderHasService('runroom.user.util.user_manipulator', UserManipulator::class);

        /**
         * @todo: Simplify this when dropping support for Symfony 4
         */
        if (class_exists(AuthenticatorManager::class)) {
            $this->assertContainerBuilderHasService('runroom.user.security.user_authenticator', UserAuthenticator::class);
        } else {
            $this->assertContainerBuilderNotHasService('runroom.user.security.user_authenticator');
        }
    }

    /**
     * @test
     */
    public function itCanEnableResetPassword(): void
    {
        $this->container->setParameter('kernel.bundles', ['SymfonyCastsResetPasswordBundle' => true]);
        $this->load(['reset_password' => ['enabled' => true]]);

        $this->assertContainerBuilderHasService('runroom.user.command.reset_password_remove_expired', ResetPasswordRemoveExpiredCommand::class);
        $this->assertContainerBuilderHasService('runroom.user.controller.reset_password', ResetPasswordController::class);
        $this->assertContainerBuilderHasService('runroom.user.form.type.change_password', ChangePasswordFormType::class);
        $this->assertContainerBuilderHasService('runroom.user.form.type.reset_password_request', ResetPasswordRequestFormType::class);
        $this->assertContainerBuilderHasService('runroom.user.service.mailer', MailerService::class);
        $this->assertContainerBuilderHasService('runroom.user.repository.reset_password_request', ResetPasswordRequestRepository::class);
        $this->assertContainerBuilderHasService('runroom.user.reset_password.cleaner', ResetPasswordCleaner::class);
        $this->assertContainerBuilderHasService('runroom.user.reset_password.helper', ResetPasswordHelper::class);
    }

    /**
     * @test
     */
    public function itCanEnableAdmin(): void
    {
        $this->container->setParameter('kernel.bundles', ['SonataAdminBundle' => true]);
        $this->load([]);

        $this->assertContainerBuilderHasService('runroom.user.admin.user', UserAdmin::class);
        $this->assertContainerBuilderHasService('runroom.user.twig.global_variables', GlobalVariables::class);
    }

    /**
     * @test
     */
    public function itCanEnableBoth(): void
    {
        $this->container->setParameter('kernel.bundles', [
            'SonataAdminBundle' => true,
            'SymfonyCastsResetPasswordBundle' => true,
        ]);
        $this->load(['reset_password' => ['enabled' => true]]);

        $this->assertContainerBuilderHasService('runroom.user.admin.reset_password_request', ResetPasswordRequestAdmin::class);
        $this->assertContainerBuilderHasService('runroom.user.admin.user', UserAdmin::class);
        $this->assertContainerBuilderHasService('runroom.user.twig.global_variables', GlobalVariables::class);
        $this->assertContainerBuilderHasService('runroom.user.command.activate_user', ActivateUserCommand::class);
        $this->assertContainerBuilderHasService('runroom.user.command.change_password', ChangePasswordCommand::class);
        $this->assertContainerBuilderHasService('runroom.user.command.create_user', CreateUserCommand::class);
        $this->assertContainerBuilderHasService('runroom.user.command.deactivate_user', DeactivateUserCommand::class);
        $this->assertContainerBuilderHasService('runroom.user.controller.security', SecurityController::class);
        $this->assertContainerBuilderHasService('runroom.user.form.type.roles_matrix', RolesMatrixType::class);
        $this->assertContainerBuilderHasService('runroom.user.repository.user', UserRepository::class);
        $this->assertContainerBuilderHasService('runroom.user.command.reset_password_remove_expired', ResetPasswordRemoveExpiredCommand::class);
        $this->assertContainerBuilderHasService('runroom.user.controller.reset_password', ResetPasswordController::class);
        $this->assertContainerBuilderHasService('runroom.user.form.type.change_password', ChangePasswordFormType::class);
        $this->assertContainerBuilderHasService('runroom.user.form.type.reset_password_request', ResetPasswordRequestFormType::class);
        $this->assertContainerBuilderHasService('runroom.user.service.mailer', MailerService::class);
        $this->assertContainerBuilderHasService('runroom.user.repository.reset_password_request', ResetPasswordRequestRepository::class);
        $this->assertContainerBuilderHasService('runroom.user.reset_password.cleaner', ResetPasswordCleaner::class);
        $this->assertContainerBuilderHasService('runroom.user.reset_password.helper', ResetPasswordHelper::class);
        $this->assertContainerBuilderHasService('runroom.user.security.roles_builder.admin', AdminRolesBuilder::class);
        $this->assertContainerBuilderHasService('runroom.user.security.roles_builder.matrix', MatrixRolesBuilder::class);
        $this->assertContainerBuilderHasService('runroom.user.security.roles_builder.security', SecurityRolesBuilder::class);
        $this->assertContainerBuilderHasService('runroom.user.twig.extension.roles_matrix', RolesMatrixExtension::class);
        $this->assertContainerBuilderHasService('runroom.user.twig.runtime.roles_matrix', RolesMatrixRuntime::class);
        $this->assertContainerBuilderHasService('runroom.user.util.user_manipulator', UserManipulator::class);

        /**
         * @todo: Simplify this when dropping support for Symfony 4
         */
        if (class_exists(AuthenticatorManager::class)) {
            $this->assertContainerBuilderHasService('runroom.user.security.user_authenticator', UserAuthenticator::class);
        } else {
            $this->assertContainerBuilderNotHasService('runroom.user.security.user_authenticator');
        }
    }

    /**
     * @test
     */
    public function itThrowsWhenTryingToEnableResetPasswordWithoutBeingInstalled(): void
    {
        $this->container->setParameter('kernel.bundles', []);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Reset password support cannot be enabled as the SymfonyCastsResetPasswordBundle is not installed or not registered. Try running "composer require symfonycasts/reset-password-bundle".');

        $this->load(['reset_password' => ['enabled' => true]]);
    }

    protected function getContainerExtensions(): array
    {
        return [new RunroomUserExtension()];
    }
}
