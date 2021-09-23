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
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;

final class MailerService
{
    private MailerInterface $mailer;
    private TranslatorInterface $translator;
    private string $fromEmail;
    private string $fromName;

    public function __construct(
        MailerInterface $mailer,
        TranslatorInterface $translator,
        string $fromEmail,
        string $fromName
    ) {
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
    }

    public function sendResetPasswordEmail(UserInterface $user, ResetPasswordToken $resetToken): void
    {
        $email = $user->getEmail();

        if (null === $email) {
            return;
        }

        $this->mailer->send((new TemplatedEmail())
            ->from(new Address($this->fromEmail, $this->fromName))
            ->to($email)
            ->subject($this->translator->trans('email.subject'))
            ->htmlTemplate('@RunroomUser/email/reset.html.twig')
            ->textTemplate('@RunroomUser/email/reset.txt.twig')
            ->context([
                'userEmail' => $email,
                'resetToken' => $resetToken,
            ]));
    }
}
