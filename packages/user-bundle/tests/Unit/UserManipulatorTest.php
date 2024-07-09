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
use Runroom\UserBundle\Repository\UserRepositoryInterface;
use Runroom\UserBundle\Util\UserManipulator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Test\Factories;

final class UserManipulatorTest extends TestCase
{
    use Factories;

    /**
     * @var MockObject&UserPasswordHasherInterface
     */
    private MockObject $passwordHasher;

    /**
     * @var MockObject&UserRepositoryInterface
     */
    private MockObject $repository;

    private string $identifier;

    private UserManipulator $userManipulator;

    protected function setUp(): void
    {
        /**
         * @todo: Simplify this when dropping support for Symfony 5
         *
         * @phpstan-ignore-next-line
         */
        $this->passwordHasher = !method_exists(UserPasswordHasherInterface::class, 'hashPassword') ?
            $this->getMockBuilder(UserPasswordHasherInterface::class)->addMethods(['hashPassword'])->getMock() :
            $this->createMock(UserPasswordHasherInterface::class);
        $this->repository = $this->createMock(UserRepositoryInterface::class);
        $this->identifier = 'user@localhost';

        $this->userManipulator = new UserManipulator(
            $this->repository,
            $this->passwordHasher
        );
    }

    public function testItCreatesUser(): void
    {
        $user = UserFactory::createOne();

        $this->repository->expects(static::once())->method('create')->willReturn($user);
        $this->repository->expects(static::once())->method('save')->with($user);

        $this->passwordHasher->expects(static::once())
            ->method('hashPassword')
            ->with($user, 'new_password')
            ->willReturn('hashed_password');

        $this->userManipulator->create('user@localhost', 'new_password', true);

        static::assertSame('user@localhost', $user->getUserIdentifier());
        static::assertSame('hashed_password', $user->getPassword());
        static::assertTrue($user->getEnabled());
        static::assertInstanceOf(\DateTimeImmutable::class, $user->getCreatedAt());
    }

    public function testItThrowsWhenActivatingANonExistentUser(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('User identified by "user@localhost" username does not exist.');

        $this->userManipulator->activate('user@localhost');
    }

    public function testItThrowsWhenDeactivatingANonExistentUser(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('User identified by "user@localhost" username does not exist.');

        $this->userManipulator->deactivate('user@localhost');
    }

    public function testItActivatesUser(): void
    {
        $user = UserFactory::createOne();

        $this->repository->method('loadUserByIdentifier')->with('user@localhost')->willReturn($user);
        $this->repository->expects(static::once())->method('save');

        $this->userManipulator->activate($this->identifier);

        static::assertTrue($user->getEnabled());
    }

    public function testItDeactivatesUser(): void
    {
        $user = UserFactory::createOne();

        $this->repository->method('loadUserByIdentifier')->with('user@localhost')->willReturn($user);
        $this->repository->expects(static::once())->method('save');

        $this->userManipulator->deactivate($this->identifier);

        static::assertFalse($user->getEnabled());
    }

    public function testItChangesPassword(): void
    {
        $user = UserFactory::createOne();

        $this->repository->method('loadUserByIdentifier')->with('user@localhost')->willReturn($user);
        $this->repository->expects(static::once())->method('save');

        $this->passwordHasher->expects(static::once())
            ->method('hashPassword')
            ->with($user, 'new_password')
            ->willReturn('hashed_password');

        $this->userManipulator->changePassword('user@localhost', 'new_password');

        static::assertSame('hashed_password', $user->getPassword());
    }
}
