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
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ActivateUserCommandTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->commandTester = new CommandTester(
            (new Application(static::createKernel()))->find('runroom:user:activate')
        );
    }

    public function testUserDoesNotExist(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->commandTester->execute(['identifier' => 'email@localhost']);
    }

    public function testUserIsAlreadyActivated(): void
    {
        $user = UserFactory::new([
            'email' => 'email@localhost',
            'enabled' => true,
        ])->create()->enableAutoRefresh()->object();
        \assert($user instanceof UserInterface);

        $this->commandTester->execute(['identifier' => 'email@localhost']);

        static::assertTrue($user->getEnabled());
    }

    public function testItActivatesDisabledUser(): void
    {
        $user = UserFactory::new([
            'email' => 'email@localhost',
            'enabled' => false,
        ])->create()->enableAutoRefresh()->object();
        \assert($user instanceof UserInterface);

        $this->commandTester->execute(['identifier' => 'email@localhost']);

        static::assertTrue($user->getEnabled());
        static::assertStringContainsString('User "email@localhost" has been activated.', $this->commandTester->getDisplay());
    }
}
