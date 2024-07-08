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
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

use function Zenstruck\Foundry\Persistence\refresh;

final class DeactivateUserCommandTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->commandTester = new CommandTester(
            (new Application(static::createKernel()))->find('runroom:user:deactivate')
        );
    }

    public function testIsThrowsWhenUserDoesNotExist(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->commandTester->execute(['identifier' => 'email@localhost']);
    }

    public function testItDoesNothingToAnAlreadyInactiveUser(): void
    {
        $user = UserFactory::createOne([
            'email' => 'email@localhost',
            'enabled' => false,
        ]);

        $this->commandTester->execute(['identifier' => 'email@localhost']);

        // @TODO: Remove else when dropping support for zenstruct/foundry 1
        if (function_exists('refresh')) {
            refresh($user);
        } else {
            $user = UserFactory::find($user->getId());
        }

        static::assertFalse($user->getEnabled());
    }

    public function testItDeactivatesUser(): void
    {
        $user = UserFactory::createOne([
            'email' => 'email@localhost',
            'enabled' => true,
        ]);

        $this->commandTester->execute(['identifier' => 'email@localhost']);

        // @TODO: Remove else when dropping support for zenstruct/foundry 1
        if (function_exists('refresh')) {
            refresh($user);
        } else {
            $user = UserFactory::find($user->getId());
        }

        static::assertFalse($user->getEnabled());
        static::assertStringContainsString('User "email@localhost" has been deactivated.', $this->commandTester->getDisplay());
    }
}
