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
use Runroom\UserBundle\Model\UserInterface;
use Runroom\UserBundle\Repository\UserRepositoryInterface;
use Runroom\UserBundle\Security\UserProvider;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

class UserProviderTest extends TestCase
{
    /** @var MockObject&UserRepositoryInterface */
    private MockObject $repository;

    private UserProvider $userProvider;

    private UserInterface $expectedUser;

    protected function setUp(): void
    {
        $this->expectedUser = new User();
        $this->expectedUser->setEmail('user@localhost');
        $this->expectedUser->setEnabled(true);
        $this->repository = $this->createMock(UserRepositoryInterface::class);
        $this->userProvider = new UserProvider($this->repository);
    }

    /** @test */
    public function itDoesntLoadsNullUserByIdentifier(): void
    {
        $this->expectException(UserNotFoundException::class);
        $this->repository->method('loadUserByIdentifier')->willReturn(null);
        $this->userProvider->loadUserByIdentifier('user@localhost');
    }

    /** @test */
    public function itDoesntLoadsDisabledUserByIdentifier(): void
    {
        $this->expectedUser->setEnabled(false);
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage(sprintf('User "%s" not found.', 'user@localhost'));
        $this->repository->method('loadUserByIdentifier')->willReturn($this->expectedUser);
        $this->userProvider->loadUserByIdentifier('user@localhost');
    }

    /** @test */
    public function itLoadsUserByIdentifier(): void
    {
        $this->repository->method('loadUserByIdentifier')->willReturn($this->expectedUser);
        $user = $this->userProvider->loadUserByIdentifier('user@localhost');

        static::assertInstanceOf(UserInterface::class, $user);
        static::assertSame($this->expectedUser, $user);
    }

    /** @test */
    public function itLoadsUserByUsername(): void
    {
        $this->repository->method('loadUserByIdentifier')->willReturn($this->expectedUser);

        $user = $this->userProvider->loadUserByUsername('user');

        static::assertInstanceOf(UserInterface::class, $user);
        static::assertSame($this->expectedUser, $user);
    }

    /** @test */
    public function itRefreshesUser(): void
    {
        $user = new User();

        $this->repository->method('loadUserByIdentifier')->with('user@localhost')->willReturn($user);

        $refreshedUser = $this->userProvider->refreshUser($this->expectedUser);

        static::assertInstanceOf(UserInterface::class, $refreshedUser);
        static::assertSame($user, $refreshedUser);
    }

    /** @test */
    public function itDoesntRefreshesNullUser(): void
    {
        $this->repository->method('loadUserByIdentifier')->willReturn(null);

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('User with identifier "user@localhost" not found.');

        $this->userProvider->refreshUser($this->expectedUser);
    }

    /** @test */
    public function itUpgradesPassword(): void
    {
        $newPassword = 'new_password';
        $this->repository->expects(static::once())->method('save')->with($this->expectedUser);
        $this->userProvider->upgradePassword($this->expectedUser, $newPassword);

        static::assertSame($this->expectedUser->getPassword(), $newPassword);
    }

    /** @test */
    public function itDoesntUpgradesPassword(): void
    {
        $user = $this->createStub(SymfonyUserInterface::class);

        $this->expectException(UnsupportedUserException::class);
        $this->expectExceptionMessage(sprintf('Instances of "%s" are not supported.', \get_class($user)));

        $this->userProvider->upgradePassword($user, 'new_password');
    }
}
