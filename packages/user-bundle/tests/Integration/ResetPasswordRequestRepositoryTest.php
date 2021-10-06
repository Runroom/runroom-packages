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

use Runroom\UserBundle\Entity\User;
use Runroom\UserBundle\Factory\ResetPasswordRequestFactory;
use Runroom\UserBundle\Factory\UserFactory;
use Runroom\UserBundle\Repository\ResetPasswordRequestRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
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

        $this->repository = self::$container->get('runroom_user.repository.reset_password_request');
    }

    /** @test */
    public function itCreatesResetPasswordRequest(): void
    {
        $user = new User();
        $date = new \DateTimeImmutable();
        $userPasswordRequest = $this->repository->createResetPasswordRequest($user, $date, 'selector', 'token');

        static::assertNotNull($userPasswordRequest);
        static::assertSame($userPasswordRequest->getUser(), $user);
        static::assertSame($userPasswordRequest->getHashedToken(), 'token');
        static::assertSame($userPasswordRequest->getExpiresAt(), $date);
    }

    /** @test */
    public function itGetsUserIdentifier(): void
    {
    }

    /** @test */
    public function itPersistsResetPasswordRequest(): void
    {
        $user = UserFactory::new()->create()->object();
        $date = new \DateTimeImmutable();
        $userPasswordRequest = $this->repository->createResetPasswordRequest($user, $date, 'selector', 'token');

        $this->repository->persistResetPasswordRequest($userPasswordRequest);

        $resetPasswordRequestResult = ResetPasswordRequestFactory::find(['user' => $user]);

        static::assertNotNull($resetPasswordRequestResult);
        static::assertSame($userPasswordRequest->getUser(), $resetPasswordRequestResult->getUser());
    }

    /** @test */
    public function itFindsResetPasswordRequestBySelector(): void
    {
        ResetPasswordRequestFactory::new([
            'selector' => 'newSelector',
            'hashedToken' => 'token',
        ])->create();

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

        ResetPasswordRequestFactory::createOne([
            'user' => $user,
        ]);

        $requestDate = $this->repository->getMostRecentNonExpiredRequestDate($user);

        static::assertNull($requestDate);
    }
}
