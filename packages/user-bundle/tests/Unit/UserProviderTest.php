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
        ])->object();

        $this->repository = $this->createMock(UserRepositoryInterface::class);

        $this->userProvider = new UserProvider($this->repository);
    }

    /**
     * @todo: Simplify exception expectation when dropping support for Symfony 4.4.
     *
     * @psalm-suppress UndefinedClass, PossiblyInvalidArgument
     */
    public function testItDoesntLoadsNullUserByIdentifier(): void
    {
        $this->repository->method('loadUserByIdentifier')->willReturn(null);

        /**
         * @todo: Simplify when dropping support for Symfony 4
         */
        $this->expectException(class_exists(UserNotFoundException::class) ? UserNotFoundException::class : UsernameNotFoundException::class);
        $this->expectExceptionMessage('User "user@localhost" not found.');

        $this->userProvider->loadUserByIdentifier('user@localhost');
    }

    /**
     * @todo: Simplify exception expectation when dropping support for Symfony 4.4.
     *
     * @psalm-suppress UndefinedClass, PossiblyInvalidArgument
     */
    public function testItDoesntLoadsDisabledUserByIdentifier(): void
    {
        $this->expectedUser->setEnabled(false);
        $this->repository->expects(static::once())->method('loadUserByIdentifier')->willReturn($this->expectedUser);

        /**
         * @todo: Simplify when dropping support for Symfony 4
         */
        $this->expectException(class_exists(UserNotFoundException::class) ? UserNotFoundException::class : UsernameNotFoundException::class);
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
        $user = UserFactory::createOne()->object();
        $this->repository->expects(static::once())->method('loadUserByIdentifier')->with('user@localhost')->willReturn($user);

        $refreshedUser = $this->userProvider->refreshUser($this->expectedUser);

        static::assertInstanceOf(UserInterface::class, $refreshedUser);
        static::assertSame($user, $refreshedUser);
    }

    /**
     * @todo: Simplify exception expectation when dropping support for Symfony 4.4.
     *
     * @psalm-suppress UndefinedClass, PossiblyInvalidArgument
     */
    public function testItDoesntRefreshesNullUser(): void
    {
        $this->repository->expects(static::once())->method('loadUserByIdentifier')->willReturn(null);

        /**
         * @todo: Simplify when dropping support for Symfony 4
         */
        $this->expectException(class_exists(UserNotFoundException::class) ? UserNotFoundException::class : UsernameNotFoundException::class);
        $this->expectExceptionMessage('User with identifier "user@localhost" not found.');

        $this->userProvider->refreshUser($this->expectedUser);
    }

    public function testItDoesntRefreshesWrongUserInstance(): void
    {
        $user = $this->createStub(SymfonyUserInterface::class);

        $this->expectException(UnsupportedUserException::class);
        $this->expectExceptionMessage(sprintf('Instances of "%s" are not supported.', \get_class($user)));

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
        $user = $this->createStub(SymfonyUserInterface::class);

        $this->expectException(UnsupportedUserException::class);
        $this->expectExceptionMessage(sprintf('Instances of "%s" are not supported.', \get_class($user)));

        $this->userProvider->upgradePassword($user, 'new_password');
    }
}
