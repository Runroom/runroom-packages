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
use Runroom\UserBundle\Repository\UserRepositoryInterface;
use Runroom\UserBundle\Util\UserManipulator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserManipulatorTest extends TestCase
{
    /** @var MockObject&UserRepositoryInterface */
    private MockObject $repository;

    /** @var (MockObject&UserPasswordHasherInterface)|null */
    private ?MockObject $passwordHasher = null;

    /** @var (MockObject&UserPasswordEncoderInterface)|null */
    private ?MockObject $passwordEncoder = null;

    private UserManipulator $userManipulator;

    private string $identifier;

    protected function setUp(): void
    {
        $this->identifier = 'user@localhost';
        $this->repository = $this->createMock(UserRepositoryInterface::class);

        /* @todo: Simplify this when dropping support for Symfony 4 */
        if (class_exists(UserPasswordHasherInterface::class)) {
            $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
            $this->userManipulator = new UserManipulator(
                $this->repository,
                $this->passwordHasher
            );
        } else {
            $this->passwordEncoder = $this->createMock(UserPasswordEncoderInterface::class);
            $this->userManipulator = new UserManipulator(
                $this->repository,
                $this->passwordEncoder
            );
        }
    }

    /** @test */
    public function itCreatesUser(): void
    {
        $user = new User();
        $identifier = 'user@localhost';
        $newPassword = 'new_password';
        $hashedPassword = 'hashed_password';

        $this->repository->expects(static::once())->method('create')->willReturn($user);
        $this->repository->expects(static::once())->method('save')->with($user);

        /* @todo: Simplify this when dropping support for Symfony 4 */
        if (null !== $this->passwordHasher) {
            $this->passwordHasher->expects(static::once())
                ->method('hashPassword')
                ->with($user, $newPassword)
                ->willReturn($hashedPassword);
        } elseif (null !== $this->passwordEncoder) {
            $this->passwordEncoder->expects(static::once())
                ->method('encodePassword')
                ->with($user, $newPassword)
                ->willReturn($hashedPassword);
        }

        $this->userManipulator->create($identifier, $newPassword, true);

        static::assertSame($user->getUserIdentifier(), $identifier);
        static::assertSame($user->getPassword(), $hashedPassword);
        static::assertSame($user->getEnabled(), true);
        static::assertInstanceOf(\DateTimeImmutable::class, $user->getCreatedAt());
    }

    /** @test */
    public function itDoesntActivatesDeactivatesUser(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('User identified by "user@localhost" username does not exist.');

        $this->userManipulator->activate($this->identifier);
        $this->userManipulator->deactivate($this->identifier);
    }

    /** @test */
    public function itActivatesUser(): void
    {
        $user = new User();
        $this->repository->method('loadUserByIdentifier')->with('user@localhost')->willReturn($user);
        $this->repository->expects(static::once())->method('save');

        $this->userManipulator->activate($this->identifier);

        static::assertSame($user->getEnabled(), true);
    }

    /** @test */
    public function itDeactivatesUser(): void
    {
        $user = new User();
        $this->repository->method('loadUserByIdentifier')->with('user@localhost')->willReturn($user);
        $this->repository->expects(static::once())->method('save');

        $this->userManipulator->deactivate($this->identifier);

        static::assertSame($user->getEnabled(), false);
    }

    /** @test */
    public function itChangesPassword(): void
    {
        $user = new User();
        $identifier = 'user@localhost';
        $newPassword = 'new_password';
        $hashedPassword = 'hashed_password';

        $this->repository->method('loadUserByIdentifier')->with('user@localhost')->willReturn($user);
        $this->repository->expects(static::once())->method('save');

        /* @todo: Simplify this when dropping support for Symfony 4 */
        if (null !== $this->passwordHasher) {
            $this->passwordHasher->expects(static::once())
                ->method('hashPassword')
                ->with($user, $newPassword)
                ->willReturn($hashedPassword);
        } elseif (null !== $this->passwordEncoder) {
            $this->passwordEncoder->expects(static::once())
                ->method('encodePassword')
                ->with($user, $newPassword)
                ->willReturn($hashedPassword);
        }

        $this->userManipulator->changePassword($identifier, $newPassword);

        static::assertSame($user->getPassword(), $hashedPassword);
    }
}
