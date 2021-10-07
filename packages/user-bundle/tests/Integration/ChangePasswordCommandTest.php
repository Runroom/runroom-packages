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
use Runroom\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Security\Http\Authentication\AuthenticatorManager;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ChangePasswordCommandTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    private CommandTester $commandTester;

    protected function setUp(): void
    {
        static::bootKernel();

        $this->commandTester = new CommandTester(
            (new Application(static::createKernel()))->find('runroom:user:change-password')
        );
    }

    /** @test */
    public function isThrowsWhenUserDoesNotExist(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->commandTester->execute([
            'identifier' => 'email@localhost',
            'password' => 'password',
        ]);
    }

    /** @test */
    public function itChangesUserPassword(): void
    {
        /** @phpstan-var Proxy<UserInterface> */
        $user = UserFactory::createOne([
            'email' => 'email@localhost',
        ])->enableAutoRefresh();

        /** @todo: Simplify this when dropping support for Symfony 4 */
        $passwordHasherId = class_exists(AuthenticatorManager::class) ? 'security.password_hasher' : 'security.password_encoder';
        $passwordHasher = static::$container->get($passwordHasherId);

        static::assertTrue($passwordHasher->isPasswordValid($user->object(), '1234'));

        $this->commandTester->execute([
            'identifier' => 'email@localhost',
            'password' => 'password',
        ]);

        static::assertTrue($passwordHasher->isPasswordValid($user->object(), 'password'));
    }
}
