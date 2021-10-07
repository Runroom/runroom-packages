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

use Runroom\UserBundle\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticatorManager;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class CreateUserCommandTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    private CommandTester $commandTester;

    protected function setUp(): void
    {
        static::bootKernel();

        $this->commandTester = new CommandTester(
            (new Application(static::createKernel()))->find('runroom:user:create')
        );
    }

    /** @test */
    public function itCreatesAnActiveUser(): void
    {
        /** @todo: Simplify this when dropping support for Symfony 4 */
        $passwordHasher = static::$container->get(class_exists(AuthenticatorManager::class) ? 'security.password_hasher' : 'security.password_encoder');
        \assert($passwordHasher instanceof UserPasswordHasherInterface || $passwordHasher instanceof UserPasswordEncoderInterface);

        $this->commandTester->execute([
            'identifier' => 'email@localhost',
            'password' => 'password',
        ]);

        $createdUser = UserFactory::find(['email' => 'email@localhost']);

        static::assertSame('email@localhost', $createdUser->getEmail());
        static::assertTrue($passwordHasher->isPasswordValid($createdUser->object(), 'password'));
        static::assertTrue($createdUser->getEnabled());
        static::assertNotNull($createdUser->getCreatedAt());
    }

    /** @test */
    public function itCreatesAnInactiveUser(): void
    {
        $this->commandTester->execute([
            'identifier' => 'email@localhost',
            'password' => 'password',
            '--inactive' => true,
        ]);

        $createdUser = UserFactory::find(['email' => 'email@localhost']);

        static::assertFalse($createdUser->getEnabled());
    }
}
