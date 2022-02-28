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
use Runroom\UserBundle\Factory\UserFactory;
use Runroom\UserBundle\Service\MailerService;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;
use Twig\Environment;
use Zenstruck\Foundry\Test\Factories;

class MailerServiceTest extends TestCase
{
    use Factories;

    /**
     * @var MockObject&MailerInterface
     */
    private MockObject $mailer;

    /**
     * @var MockObject&TranslatorInterface
     */
    private MockObject $translator;

    private MailerService $service;

    protected function setUp(): void
    {
        $this->mailer = $this->createMock(MailerInterface::class);
        $this->translator = $this->createMock(TranslatorInterface::class);

        $this->service = new MailerService(
            $this->mailer,
            $this->translator,
            $this->createStub(Environment::class),
            'user@email.com',
            'userName'
        );
    }

    /**
     * @test
     */
    public function itCallsMailerWhenUserHasEmail(): void
    {
        $user = UserFactory::createOne(['email' => 'user@email.com'])->object();
        $resetPasswordToken = new ResetPasswordToken('token', new \DateTimeImmutable(), 0);

        $this->translator->method('trans')->with('email.subject')->willReturn('Subject');
        $this->mailer->expects(static::once())->method('send');

        $this->service->sendResetPasswordEmail($user, $resetPasswordToken);
    }

    /**
     * @test
     */
    public function itDoesntCallMailerWhenUserDoesntHaveEmail(): void
    {
        $user = UserFactory::createOne(['email' => null])->object();
        $resetPasswordToken = new ResetPasswordToken('token', new \DateTimeImmutable(), 0);

        $this->mailer->expects(static::never())->method('send');

        $this->service->sendResetPasswordEmail($user, $resetPasswordToken);
    }
}
