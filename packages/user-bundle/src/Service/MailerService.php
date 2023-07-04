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

namespace Runroom\UserBundle\Service;

use Runroom\UserBundle\Model\UserInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;
use Twig\Environment;

final class MailerService implements MailerServiceInterface
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly TranslatorInterface $translator,
        private readonly Environment $twig,
        private readonly string $fromEmail,
        private readonly string $fromName
    ) {
    }

    public function sendResetPasswordEmail(UserInterface $user, ResetPasswordToken $resetToken): void
    {
        $email = $user->getEmail();

        if (null === $email) {
            return;
        }

        $this->mailer->send(
            (new Email())
                ->from(new Address($this->fromEmail, $this->fromName))
                ->to($email)
                ->subject($this->translator->trans('email.subject'))
                ->html($this->twig->render('@RunroomUser/email/reset.html.twig', [
                    'userEmail' => $email,
                    'resetToken' => $resetToken,
                ]))
                ->text($this->twig->render('@RunroomUser/email/reset.txt.twig', [
                    'userEmail' => $email,
                    'resetToken' => $resetToken,
                ]))
        );
    }
}
