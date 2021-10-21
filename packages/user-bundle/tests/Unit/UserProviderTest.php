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
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class UserProviderTest extends TestCase
{
    /** @var MockObject&UserRepositoryInterface */
    private MockObject $repository;

    private UserProvider $userProvider;

    private UserInterface $user;

    protected function setUp(): void
    {
        $this->user = new User();
        $this->user->setEmail('user@localhost');
        $this->user->setEnabled(true);
        $this->repository = $this->createMock(UserRepositoryInterface::class);
        $this->userProvider = new UserProvider($this->repository);
    }

    /** @test */
    public function itDoestLoadsUserByIdentifierWithNull(): void
    {
        $this->expectException(UserNotFoundException::class);
        $this->repository->method('loadUserByIdentifier')->willReturn(null);
        $this->userProvider->loadUserByIdentifier('user@localhost');
    }

    /** @test */
    public function itDoesntLoadDisabledUserByIdentifier(): void
    {
        $this->user->setEnabled(false);
        $this->expectException(UserNotFoundException::class);
        $this->repository->method('loadUserByIdentifier')->willReturn($this->user);
        $this->userProvider->loadUserByIdentifier('user@localhost');
    }

    /** @test */
    public function itLoadsUserByIdentifier(): void
    {
        $this->repository->method('loadUserByIdentifier')->willReturn($this->user);
        $result = $this->userProvider->loadUserByIdentifier('email@localhost');

        static::assertInstanceOf(UserInterface::class, $result);
        static::assertSame($this->user, $result);
    }
}
