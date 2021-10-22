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
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;
use Zenstruck\Foundry\Test\Factories;

class UserProviderTest extends TestCase
{
    use Factories;

    private UserInterface $expectedUser;

    /** @var MockObject&UserRepositoryInterface */
    private MockObject $repository;

    private UserProvider $userProvider;

    protected function setUp(): void
    {
        $this->expectedUser = UserFactory::createOne([
            'email' => 'user@localhost',
            'enabled' => true,
        ])->object();

        $this->repository = $this->createMock(UserRepositoryInterface::class);

        $this->userProvider = new UserProvider($this->repository);
    }

    /** @test */
    public function itDoesntLoadsNullUserByIdentifier(): void
    {
        $this->repository->method('loadUserByIdentifier')->willReturn(null);

        /* @todo: Simplify when dropping support for Symfony 4 */
        $this->expectException(class_exists(UserNotFoundException::class) ? UserNotFoundException::class : UsernameNotFoundException::class);
        $this->expectExceptionMessage('User "user@localhost" not found.');

        $this->userProvider->loadUserByIdentifier('user@localhost');
    }

    /** @test */
    public function itDoesntLoadsDisabledUserByIdentifier(): void
    {
        $this->expectedUser->setEnabled(false);
        $this->repository->expects(static::once())->method('loadUserByIdentifier')->willReturn($this->expectedUser);

        /* @todo: Simplify when dropping support for Symfony 4 */
        $this->expectException(class_exists(UserNotFoundException::class) ? UserNotFoundException::class : UsernameNotFoundException::class);
        $this->expectExceptionMessage('User "user@localhost" not found.');

        $this->userProvider->loadUserByIdentifier('user@localhost');
    }

    /** @test */
    public function itLoadsUserByIdentifier(): void
    {
        $this->repository->expects(static::once())->method('loadUserByIdentifier')->willReturn($this->expectedUser);

        $user = $this->userProvider->loadUserByIdentifier('user@localhost');

        static::assertInstanceOf(UserInterface::class, $user);
        static::assertSame($this->expectedUser, $user);
    }

    /** @test */
    public function itLoadsUserByUsername(): void
    {
        $this->repository->expects(static::once())->method('loadUserByIdentifier')->willReturn($this->expectedUser);

        $user = $this->userProvider->loadUserByUsername('user');

        static::assertInstanceOf(UserInterface::class, $user);
        static::assertSame($this->expectedUser, $user);
    }

    /** @test */
    public function itRefreshesUser(): void
    {
        $user = UserFactory::createOne()->object();
        $this->repository->expects(static::once())->method('loadUserByIdentifier')->with('user@localhost')->willReturn($user);

        $refreshedUser = $this->userProvider->refreshUser($this->expectedUser);

        static::assertInstanceOf(UserInterface::class, $refreshedUser);
        static::assertSame($user, $refreshedUser);
    }

    /** @test */
    public function itDoesntRefreshesNullUser(): void
    {
        $this->repository->expects(static::once())->method('loadUserByIdentifier')->willReturn(null);

        /* @todo: Simplify when dropping support for Symfony 4 */
        $this->expectException(class_exists(UserNotFoundException::class) ? UserNotFoundException::class : UsernameNotFoundException::class);
        $this->expectExceptionMessage('User with identifier "user@localhost" not found.');

        $this->userProvider->refreshUser($this->expectedUser);
    }

    /** @test */
    public function itDoesntRefreshesWrongUserInstance(): void
    {
        $user = $this->createStub(SymfonyUserInterface::class);

        $this->expectException(UnsupportedUserException::class);
        $this->expectExceptionMessage(sprintf('Instances of "%s" are not supported.', \get_class($user)));

        $this->userProvider->refreshUser($user);
    }

    /** @test */
    public function itUpgradesPassword(): void
    {
        $this->repository->expects(static::once())->method('save')->with($this->expectedUser);

        $this->userProvider->upgradePassword($this->expectedUser, 'new_password');

        static::assertSame('new_password', $this->expectedUser->getPassword());
    }

    /** @test */
    public function itDoesntUpgradePasswordForWrongUserInstance(): void
    {
        $user = $this->createStub(SymfonyUserInterface::class);

        $this->expectException(UnsupportedUserException::class);
        $this->expectExceptionMessage(sprintf('Instances of "%s" are not supported.', \get_class($user)));

        $this->userProvider->upgradePassword($user, 'new_password');
    }
}
