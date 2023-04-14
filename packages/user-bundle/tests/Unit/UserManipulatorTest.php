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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Zenstruck\Foundry\Test\Factories;

class UserManipulatorTest extends TestCase
{
    use Factories;

    /**
     * @var (MockObject&UserPasswordHasherInterface)|null
     */
    private ?MockObject $passwordHasher = null;

    /**
     * @psalm-suppress UndefinedDocblockClass
     *
     * @var (MockObject&UserPasswordEncoderInterface)|null
     */
    private ?MockObject $passwordEncoder = null;

    /**
     * @var MockObject&UserRepositoryInterface
     */
    private MockObject $repository;

    private string $identifier;

    private UserManipulator $userManipulator;

    protected function setUp(): void
    {
        /**
         * @todo: Simplify this when dropping support for Symfony 4
         */
        if (interface_exists(UserPasswordHasherInterface::class) && !method_exists(UserPasswordHasherInterface::class, 'hashPassword')) {
            $this->passwordHasher = $this->getMockBuilder(UserPasswordHasherInterface::class)
                ->addMethods(['hashPassword'])->getMock();
        } elseif (interface_exists(UserPasswordHasherInterface::class)) {
            $this->passwordHasher = $this->getMockBuilder(UserPasswordHasherInterface::class)->getMock();
        } else {
            /**
             * @psalm-suppress PropertyTypeCoercion
             */
            $this->passwordEncoder = $this->createMock(UserPasswordEncoderInterface::class);
        }
        $passwordHasher = $this->passwordHasher;
        $passwordEncoder = $this->passwordEncoder;
        \assert(null !== $passwordHasher || null !== $passwordEncoder);

        $this->repository = $this->createMock(UserRepositoryInterface::class);
        $this->identifier = 'user@localhost';

        $this->userManipulator = new UserManipulator(
            $this->repository,
            $passwordHasher ?? $passwordEncoder
        );
    }

    /**
     * @test
     */
    public function itCreatesUser(): void
    {
        $user = UserFactory::createOne()->object();

        $this->repository->expects(static::once())->method('create')->willReturn($user);
        $this->repository->expects(static::once())->method('save')->with($user);

        /**
         * @todo: Simplify this when dropping support for Symfony 4
         */
        if (null !== $this->passwordHasher) {
            $this->passwordHasher->expects(static::once())
                ->method('hashPassword')
                ->with($user, 'new_password')
                ->willReturn('hashed_password');
        } elseif (null !== $this->passwordEncoder) {
            /**
             * @psalm-suppress UndefinedDocblockClass
             */
            $this->passwordEncoder->expects(static::once())
                ->method('encodePassword')
                ->with($user, 'new_password')
                ->willReturn('hashed_password');
        }

        $this->userManipulator->create('user@localhost', 'new_password', true);

        static::assertSame('user@localhost', $user->getUserIdentifier());
        static::assertSame('hashed_password', $user->getPassword());
        static::assertTrue($user->getEnabled());
        static::assertInstanceOf(\DateTimeImmutable::class, $user->getCreatedAt());
    }

    /**
     * @test
     */
    public function itThrowsWhenActivatingANonExistentUser(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('User identified by "user@localhost" username does not exist.');

        $this->userManipulator->activate('user@localhost');
    }

    /**
     * @test
     */
    public function itThrowsWhenDeactivatingANonExistentUser(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('User identified by "user@localhost" username does not exist.');

        $this->userManipulator->deactivate('user@localhost');
    }

    /**
     * @test
     */
    public function itActivatesUser(): void
    {
        $user = UserFactory::createOne()->object();

        $this->repository->method('loadUserByIdentifier')->with('user@localhost')->willReturn($user);
        $this->repository->expects(static::once())->method('save');

        $this->userManipulator->activate($this->identifier);

        static::assertTrue($user->getEnabled());
    }

    /**
     * @test
     */
    public function itDeactivatesUser(): void
    {
        $user = UserFactory::createOne()->object();

        $this->repository->method('loadUserByIdentifier')->with('user@localhost')->willReturn($user);
        $this->repository->expects(static::once())->method('save');

        $this->userManipulator->deactivate($this->identifier);

        static::assertFalse($user->getEnabled());
    }

    /**
     * @test
     */
    public function itChangesPassword(): void
    {
        $user = UserFactory::createOne()->object();

        $this->repository->method('loadUserByIdentifier')->with('user@localhost')->willReturn($user);
        $this->repository->expects(static::once())->method('save');

        /**
         * @todo: Simplify this when dropping support for Symfony 4
         */
        if (null !== $this->passwordHasher) {
            $this->passwordHasher->expects(static::once())
                ->method('hashPassword')
                ->with($user, 'new_password')
                ->willReturn('hashed_password');
        } elseif (null !== $this->passwordEncoder) {
            /**
             * @psalm-suppress UndefinedDocblockClass
             */
            $this->passwordEncoder->expects(static::once())
                ->method('encodePassword')
                ->with($user, 'new_password')
                ->willReturn('hashed_password');
        }

        $this->userManipulator->changePassword('user@localhost', 'new_password');

        static::assertSame('hashed_password', $user->getPassword());
    }
}
