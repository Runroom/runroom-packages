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

namespace Runroom\UserBundle\Tests\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Runroom\UserBundle\Entity\User;
use Runroom\UserBundle\Service\MailerService;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;

class MailerServiceTest extends TestCase
{
    /** @var MockObject&MailerInterface */
    private $mailer;
    /** @var MockObject&TranslatorInterface */
    private $translator;

    private MailerService $service;

    private string $fromEmail;

    protected function setUp(): void
    {
        $this->mailer = $this->createMock(MailerInterface::class);
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->fromEmail = 'user@email.com';

        $this->service = new MailerService(
            $this->mailer,
            $this->translator,
            $this->fromEmail,
            'userName'
        );
    }

    /** @test */
    public function itCallsMailerWhenUserHasEmail(): void
    {
        $user = new User();
        $user->setEmail($this->fromEmail);
        $resetPasswordToken = new ResetPasswordToken(
            'token',
            new \DateTime()
        );

        $this->translator->method('trans')->with('email.subject')->willReturn('Subject');

        $this->mailer->expects(static::once())
            ->method('send');

        $this->service->sendResetPasswordEmail($user, $resetPasswordToken);
    }

    /** @test */
    public function itDoesntCallMailerWhenUserDoesntHaveEmail(): void
    {
        $user = new User();
        $resetPasswordToken = new ResetPasswordToken(
            'token',
            new \DateTime()
        );

        $this->translator->method('trans')->with('email.subject')->willReturn('Subject');

        $this->mailer->expects(static::never())
            ->method('send');

        $this->service->sendResetPasswordEmail($user, $resetPasswordToken);
    }
}
