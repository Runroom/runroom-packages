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

namespace Runroom\UserBundle\Controller;

use Runroom\UserBundle\Form\ChangePasswordFormType;
use Runroom\UserBundle\Form\ResetPasswordRequestFormType;
use Runroom\UserBundle\Model\UserInterface;
use Runroom\UserBundle\Security\UserProvider;
use Runroom\UserBundle\Service\MailerServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

final class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(
        private readonly ResetPasswordHelperInterface $resetPasswordHelper,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly MailerServiceInterface $mailerService,
        private readonly UserProvider $userProvider
    ) {}

    public function request(Request $request): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $identifier = $form->get('identifier')->getData();
            \assert(\is_string($identifier));

            $this->processSendingPasswordResetEmail($identifier);

            return $this->redirectToRoute('runroom_user_check_email');
        }

        return $this->render('@RunroomUser/reset_password/request.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    public function checkEmail(): Response
    {
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }

        return $this->render('@RunroomUser/reset_password/check_email.html.twig', [
            'resetToken' => $resetToken,
        ]);
    }

    public function reset(Request $request, ?string $token = null): Response
    {
        if (null !== $token) {
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('runroom_user_reset_password');
        }

        $token = $this->getTokenFromSession();
        if (null === $token) {
            throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
        }

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
            \assert($user instanceof UserInterface);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('reset_password_error', sprintf(
                'There was a problem validating your reset request - %s',
                $e->getReason()
            ));

            return $this->redirectToRoute('runroom_user_forgot_password_request');
        }

        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->resetPasswordHelper->removeResetRequest($token);

            $plainPassword = $form->get('plainPassword')->getData();
            \assert(\is_string($plainPassword));

            $password = $this->passwordHasher->hashPassword($user, $plainPassword);

            $this->userProvider->upgradePassword($user, $password);

            $this->cleanSessionAfterReset();

            return $this->redirectToRoute('sonata_admin_dashboard');
        }

        return $this->render('@RunroomUser/reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }

    private function processSendingPasswordResetEmail(string $identifier): void
    {
        try {
            $user = $this->userProvider->loadUserByIdentifier($identifier);
            \assert($user instanceof UserInterface);

            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (AuthenticationException|ResetPasswordExceptionInterface) {
            return;
        }

        $this->mailerService->sendResetPasswordEmail($user, $resetToken);
        $this->setTokenObjectInSession($resetToken);
    }
}
