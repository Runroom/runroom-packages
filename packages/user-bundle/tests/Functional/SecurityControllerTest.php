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

namespace Runroom\UserBundle\Tests\Functional;

use Runroom\UserBundle\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Http\Authentication\AuthenticatorManager;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class SecurityControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    /** @test */
    public function itRendersLoginPage(): void
    {
        $client = static::createClient();

        $client->request('GET', '/login');

        self::assertResponseIsSuccessful();
    }

    /** @test */
    public function itSubmitsLoginForm(): void
    {
        $client = static::createClient();

        UserFactory::new([
            'email' => 'email@localhost',
            'enabled' => true,
        ])->create();

        $client->request('GET', '/login');
        $client->submitForm('submit', [
            '_username' => 'email@localhost',
            '_password' => UserFactory::DEFAULT_PASSWORD,
        ]);

        self::assertResponseRedirects(class_exists(AuthenticatorManager::class) ? '/dashboard' : 'http://localhost/dashboard');
    }

    /** @test */
    public function itSubmitsLoginFormWithDisabledUser(): void
    {
        $client = static::createClient();

        UserFactory::new([
            'email' => 'email@localhost',
            'enabled' => false,
        ])->create();

        $client->request('GET', '/login');
        $client->submitForm('submit', [
            '_username' => 'email@localhost',
            '_password' => UserFactory::DEFAULT_PASSWORD,
        ]);

        self::assertResponseRedirects(class_exists(AuthenticatorManager::class) ? '/login' : 'http://localhost/login');
    }
}
