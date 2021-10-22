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

namespace Runroom\UserBundle\Tests\Integration;

use Runroom\UserBundle\Entity\User;
use Runroom\UserBundle\Factory\UserFactory;
use Runroom\UserBundle\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class UserRepositoryTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    private UserRepository $repository;

    protected function setUp(): void
    {
        parent::bootKernel();

        /** @todo: Simplify this when dropping support for Symfony 4 */
        $container = method_exists(static::class, 'getContainer') ? static::getContainer() : static::$container;

        $this->repository = $container->get('runroom_user.repository.user');
    }

    /** @test */
    public function itFindsUserGivenItsSlug(): void
    {
        UserFactory::createOne([
            'email' => 'email@localhost',
            'enabled' => true,
        ]);

        $user = $this->repository->loadUserByIdentifier('email@localhost');

        static::assertInstanceOf(User::class, $user);
        static::assertSame(1, $user->getId());
        static::assertSame('email@localhost', (string) $user);
        static::assertSame('email@localhost', $user->getEmail());
        static::assertSame('email@localhost', $user->getUserIdentifier());
        static::assertSame('email@localhost', $user->getUsername());
        static::assertSame(['ROLE_USER'], $user->getRoles());
        static::assertNotEmpty($user->getPassword());
        static::assertTrue($user->getEnabled());
        static::assertNotEmpty($user->getCreatedAt());
        static::assertNull($user->getSalt());
    }

    /** @test */
    public function itCreatesAnUser(): void
    {
        $user = $this->repository->create();

        static::assertInstanceOf(User::class, $user);
    }

    /** @test */
    public function itPersistAnUser(): void
    {
        $user = $this->repository->create();
        $user->setEmail('email@localhost');
        $user->setPassword('password');
        $user->setCreatedAt(new \DateTimeImmutable());

        $this->repository->save($user);

        $foundUser = UserFactory::find(['email' => 'email@localhost']);

        static::assertSame('password', $foundUser->getPassword());
    }
}
