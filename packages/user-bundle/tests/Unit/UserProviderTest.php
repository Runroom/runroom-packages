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
use Runroom\UserBundle\Model\UserInterface;
use Runroom\UserBundle\Repository\UserRepositoryInterface;
use Runroom\UserBundle\Security\UserProvider;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;
use Zenstruck\Foundry\Test\Factories;

final class UserProviderTest extends TestCase
{
    use Factories;

    private UserInterface $expectedUser;

    /**
     * @var MockObject&UserRepositoryInterface
     */
    private MockObject $repository;

    private UserProvider $userProvider;

    protected function setUp(): void
    {
        $this->expectedUser = UserFactory::createOne([
            'email' => 'user@localhost',
            'enabled' => true,
        ]);

        $this->repository = $this->createMock(UserRepositoryInterface::class);

        $this->userProvider = new UserProvider($this->repository);
    }

    public function testItDoesntLoadsNullUserByIdentifier(): void
    {
        $this->repository->method('loadUserByIdentifier')->willReturn(null);

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('User "user@localhost" not found.');

        $this->userProvider->loadUserByIdentifier('user@localhost');
    }

    public function testItDoesntLoadsDisabledUserByIdentifier(): void
    {
        $this->expectedUser->setEnabled(false);
        $this->repository->expects(static::once())->method('loadUserByIdentifier')->willReturn($this->expectedUser);

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('User "user@localhost" not found.');

        $this->userProvider->loadUserByIdentifier('user@localhost');
    }

    public function testItLoadsUserByIdentifier(): void
    {
        $this->repository->expects(static::once())->method('loadUserByIdentifier')->willReturn($this->expectedUser);

        $user = $this->userProvider->loadUserByIdentifier('user@localhost');

        static::assertInstanceOf(UserInterface::class, $user);
        static::assertSame($this->expectedUser, $user);
    }

    public function testItLoadsUserByUsername(): void
    {
        $this->repository->expects(static::once())->method('loadUserByIdentifier')->willReturn($this->expectedUser);

        $user = $this->userProvider->loadUserByUsername('user');

        static::assertInstanceOf(UserInterface::class, $user);
        static::assertSame($this->expectedUser, $user);
    }

    public function testItRefreshesUser(): void
    {
        $user = UserFactory::createOne();
        $this->repository->expects(static::once())->method('loadUserByIdentifier')->with('user@localhost')->willReturn($user);

        $refreshedUser = $this->userProvider->refreshUser($this->expectedUser);

        static::assertInstanceOf(UserInterface::class, $refreshedUser);
        static::assertSame($user, $refreshedUser);
    }

    public function testItDoesntRefreshesNullUser(): void
    {
        $this->repository->expects(static::once())->method('loadUserByIdentifier')->willReturn(null);

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('User with identifier "user@localhost" not found.');

        $this->userProvider->refreshUser($this->expectedUser);
    }

    public function testItDoesntRefreshesWrongUserInstance(): void
    {
        $user = static::createStub(SymfonyUserInterface::class);

        $this->expectException(UnsupportedUserException::class);
        $this->expectExceptionMessage(\sprintf('Instances of "%s" are not supported.', $user::class));

        $this->userProvider->refreshUser($user);
    }

    public function testItUpgradesPassword(): void
    {
        $this->repository->expects(static::once())->method('save')->with($this->expectedUser);

        $this->userProvider->upgradePassword($this->expectedUser, 'new_password');

        static::assertSame('new_password', $this->expectedUser->getPassword());
    }

    public function testItDoesntUpgradePasswordForWrongUserInstance(): void
    {
        $user = static::createStub(SymfonyUserInterface::class);

        $this->expectException(UnsupportedUserException::class);
        $this->expectExceptionMessage(\sprintf('Instances of "%s" are not supported.', $user::class));

        $this->userProvider->upgradePassword($user, 'new_password');
    }
}
