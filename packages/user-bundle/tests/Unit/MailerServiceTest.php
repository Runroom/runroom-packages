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
use Twig\Environment;

class MailerServiceTest extends TestCase
{
    /** @var MockObject&MailerInterface */
    private $mailer;

    /** @var MockObject&TranslatorInterface */
    private $translator;

    /** @var MockObject&Environment */
    private $twig;

    private MailerService $service;

    protected function setUp(): void
    {
        $this->mailer = $this->createMock(MailerInterface::class);
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->twig = $this->createMock(Environment::class);

        $this->service = new MailerService(
            $this->mailer,
            $this->translator,
            $this->twig,
            'user@email.com',
            'userName'
        );
    }

    /** @test */
    public function itCallsMailerWhenUserHasEmail(): void
    {
        $user = new User();
        $user->setEmail('user@email.com');
        $resetPasswordToken = new ResetPasswordToken(
            'token',
            new \DateTime(),
            0
        );

        $this->translator->method('trans')->with('email.subject')->willReturn('Subject');

        $this->mailer->expects(static::once())->method('send');

        $this->service->sendResetPasswordEmail($user, $resetPasswordToken);
    }

    /** @test */
    public function itDoesntCallMailerWhenUserDoesntHaveEmail(): void
    {
        $user = new User();
        $resetPasswordToken = new ResetPasswordToken(
            'token',
            new \DateTimeImmutable(),
            0
        );

        $this->translator->method('trans')->with('email.subject')->willReturn('Subject');

        $this->mailer->expects(static::never())->method('send');

        $this->service->sendResetPasswordEmail($user, $resetPasswordToken);
    }
}
