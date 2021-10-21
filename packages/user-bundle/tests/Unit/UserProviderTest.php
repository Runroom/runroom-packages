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

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Runroom\UserBundle\Entity\User;
use Runroom\UserBundle\Model\UserInterface;
use Runroom\UserBundle\Repository\UserRepository;
use Runroom\UserBundle\Security\UserProvider;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Zenstruck\Foundry\Test\Factories;

class UserProviderTest extends TestCase
{
    use Factories;

    /** @var MockObject&EntityManagerInterface */
    private MockObject $entityManager;

    private UserRepository $repository;

    private UserProvider $userProvider;

    /** @var MockObject&EntityRepository */
    private MockObject $entityRepository;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->entityRepository = $this->createMock(EntityRepository::class);
        $this->entityManager->method('getRepository')->willReturn($this->entityRepository);
        $this->repository = new UserRepository($this->entityManager, UserInterface::class);
        $this->userProvider = new UserProvider($this->repository);
    }

    /** @test */
    public function itDoestLoadsUserByIdentifier(): void
    {
        $this->expectException(UserNotFoundException::class);
        $this->userProvider->loadUserByIdentifier('user@localhost');
    }

    /** @test */
    public function itLoadsUserByIdentifier(): void
    {
        $user = new User();
        $user->setEmail('email@localhost');
        $user->setEnabled(true);
        $this->entityRepository->method('findOneBy')->willReturn($user);
        $result = $this->userProvider->loadUserByIdentifier('email@localhost');

        static::assertSame($user, $result);
    }
}
