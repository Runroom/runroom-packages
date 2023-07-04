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
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class CreateUserCommandTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->commandTester = new CommandTester(
            (new Application(static::createKernel()))->find('runroom:user:create')
        );
    }

    public function testItCreatesAnActiveUser(): void
    {
        $passwordHasher = static::getContainer()->get('security.password_hasher');
        \assert($passwordHasher instanceof UserPasswordHasherInterface);

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

    public function testItCreatesAnInactiveUser(): void
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
