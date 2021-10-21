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

    /** @var MockObject&(UserPasswordHasherInterface|UserPasswordEncoderInterface) */
    private MockObject $passwordHasher;

    private UserManipulator $userManipulator;

    private string $identifier;

    protected function setUp(): void
    {
        $this->identifier = 'user@localhost';
        $this->repository = $this->createMock(UserRepositoryInterface::class);

        if (!class_exists(UserPasswordHasherInterface::class)) {
            $this->passwordHasher = $this->createMock(UserPasswordEncoderInterface::class);
        } else {
            $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        }

        $this->userManipulator = new UserManipulator(
            $this->repository,
            $this->passwordHasher
        );
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
        if ($this->passwordHasher instanceof UserPasswordEncoderInterface) {
            $this->passwordHasher->expects(static::once())
                ->method('encodePassword')
                ->with($user, $newPassword)
                ->willReturn($hashedPassword);
        } else {
            $this->passwordHasher->expects(static::once())
                ->method('hashPassword')
                ->with($user, $newPassword)
                ->willReturn($hashedPassword);
        }

        $this->userManipulator->create($identifier, $newPassword, true);

        static::assertSame($user->getUserIdentifier(), $identifier);
        static::assertSame($user->getPassword(), $hashedPassword);
        static::assertSame($user->getEnabled(), true);
    }
}
