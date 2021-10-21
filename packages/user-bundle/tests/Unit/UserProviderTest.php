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
        $this->repository->method('loadUserByIdentifier')->willReturn($this->expectedUser);
        $refreshedUser = $this->userProvider->refreshUser($this->expectedUser);

        static::assertInstanceOf(UserInterface::class, $refreshedUser);
        static::assertSame($this->expectedUser, $refreshedUser);
    }

    /** @test */
    public function itDoesntRefreshesNullUser(): void
    {
        $this->repository->method('loadUserByIdentifier')->willReturn(null);
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage(sprintf('User with id "%s" not found.', $this->expectedUser->getId()));
        $this->userProvider->refreshUser($this->expectedUser);
    }
}
