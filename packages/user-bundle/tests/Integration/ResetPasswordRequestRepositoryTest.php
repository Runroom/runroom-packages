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

use Runroom\UserBundle\Factory\ResetPasswordRequestFactory;
use Runroom\UserBundle\Factory\UserFactory;
use Runroom\UserBundle\Repository\ResetPasswordRequestRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ResetPasswordRequestRepositoryTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    private ResetPasswordRequestRepository $repository;

    protected function setUp(): void
    {
        parent::bootKernel();

        /** @todo: Simplify this when dropping support for Symfony 4 */
        $container = method_exists(static::class, 'getContainer') ? static::getContainer() : static::$container;

        $this->repository = $container->get('runroom_user.repository.reset_password_request');
    }

    /** @test */
    public function itCreatesResetPasswordRequest(): void
    {
        $user = UserFactory::createOne()->object();
        $date = new \DateTimeImmutable();
        $userPasswordRequest = $this->repository->createResetPasswordRequest($user, $date, 'selector', 'token');

        static::assertInstanceOf(ResetPasswordRequestInterface::class, $userPasswordRequest);
        static::assertSame($userPasswordRequest->getUser(), $user);
        static::assertSame($userPasswordRequest->getHashedToken(), 'token');
        static::assertSame($userPasswordRequest->getExpiresAt(), $date);
    }

    /** @test */
    public function itGetsUserIdentifier(): void
    {
        $user = UserFactory::createOne()->object();
        $identifier = $this->repository->getUserIdentifier($user);

        static::assertSame('1', $identifier);
    }

    /** @test */
    public function itPersistsResetPasswordRequest(): void
    {
        $user = UserFactory::createOne()->object();
        $date = new \DateTimeImmutable();
        $userPasswordRequest = $this->repository->createResetPasswordRequest($user, $date, 'selector', 'token');

        $this->repository->persistResetPasswordRequest($userPasswordRequest);

        $foundResetPasswordRequest = ResetPasswordRequestFactory::find(['user' => $user]);

        static::assertNotNull($foundResetPasswordRequest->getId());
        static::assertSame($userPasswordRequest->getUser(), $foundResetPasswordRequest->getUser());
    }

    /** @test */
    public function itFindsResetPasswordRequestBySelector(): void
    {
        ResetPasswordRequestFactory::createOne([
            'selector' => 'newSelector',
            'hashedToken' => 'token',
        ]);

        $resetPasswordRequest = $this->repository->findResetPasswordRequest('newSelector');

        static::assertNotNull($resetPasswordRequest);
        static::assertSame('token', $resetPasswordRequest->getHashedToken());
    }

    /** @test */
    public function itGetsNullWhenThereIsNoMostRecentExpiredRequestPassword(): void
    {
        $user = UserFactory::createOne()->object();
        $requestDate = $this->repository->getMostRecentNonExpiredRequestDate($user);

        static::assertNull($requestDate);
    }

    /** @test */
    public function itGetsNullWhenThereIsAExpiredMostRecentRequestPassword(): void
    {
        $user = UserFactory::createOne()->object();
        ResetPasswordRequestFactory::createOne(['user' => $user]);

        $requestDate = $this->repository->getMostRecentNonExpiredRequestDate($user);

        static::assertNull($requestDate);
    }

    /** @test */
    public function itGetsTheMostNonExpiredRequestDate(): void
    {
        $user = UserFactory::createOne()->object();
        $date = new \DateTimeImmutable();

        ResetPasswordRequestFactory::createOne([
            'user' => $user,
            'expiresAt' => $date->add(new \DateInterval('PT1H')),
        ]);

        $requestDate = $this->repository->getMostRecentNonExpiredRequestDate($user);

        static::assertNotNull($requestDate);
    }

    /** @test */
    public function itGetsTheMostNonExpiredRequestDateWithTwoResetPasswords(): void
    {
        $user = UserFactory::createOne()->object();
        $date = new \DateTimeImmutable();
        $oneHour = $date->add(new \DateInterval('PT1H'));
        $twoHours = $date->add(new \DateInterval('PT2H'));

        ResetPasswordRequestFactory::createOne([
            'user' => $user,
            'expiresAt' => $oneHour,
            'requestedAt' => $oneHour,
        ]);

        $secondResetPasswordRequest = ResetPasswordRequestFactory::createOne([
            'user' => $user,
            'expiresAt' => $oneHour,
            'requestedAt' => $twoHours,
        ])->object();

        $requestDate = $this->repository->getMostRecentNonExpiredRequestDate($user);

        static::assertNotNull($requestDate);
        static::assertSame($secondResetPasswordRequest->getRequestedAt(), $requestDate);
    }

    /** @test */
    public function itCanRemoveExpiredResetPasswordByObject(): void
    {
        $user = UserFactory::createOne()->object();
        $resetPasswordRequest = ResetPasswordRequestFactory::createOne([
            'user' => $user,
            'selector' => 'newSelector',
        ])->object();

        $this->repository->removeResetPasswordRequest($resetPasswordRequest);
        $resetPasswordRequestResult = $this->repository->findResetPasswordRequest('newSelector');

        static::assertNull($resetPasswordRequestResult);
    }

    /** @test */
    public function itCanRemoveExpiredResetPasswords(): void
    {
        $user = UserFactory::createOne()->object();

        ResetPasswordRequestFactory::createOne([
            'user' => $user,
            'expiresAt' => new \DateTimeImmutable('-2 week'),
            'selector' => 'newSelector',
        ]);

        $this->repository->removeExpiredResetPasswordRequests();
        $resetPasswordRequestResult = $this->repository->findResetPasswordRequest('newSelector');

        static::assertNull($resetPasswordRequestResult);
    }
}
