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
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class SecurityControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testItSubmitsLoginForm(): void
    {
        $client = static::createClient();

        UserFactory::createOne([
            'email' => 'email@localhost',
            'password' => 'random_password',
            'enabled' => true,
        ]);

        $client->request('GET', '/login');

        static::assertResponseIsSuccessful();

        $client->submitForm('submit', [
            '_username' => 'email@localhost',
            '_password' => 'random_password',
        ]);
        $client->followRedirect();

        static::assertRouteSame('sonata_admin_dashboard');

        $client->request('GET', '/login');
        $client->followRedirect();

        static::assertRouteSame('sonata_admin_dashboard');
    }

    public function testItSubmitsLoginFormWithDisabledUser(): void
    {
        $client = static::createClient();

        UserFactory::createOne([
            'email' => 'email@localhost',
            'password' => 'random_password',
            'enabled' => false,
        ]);

        $client->request('GET', '/login');

        static::assertResponseIsSuccessful();

        $client->submitForm('submit', [
            '_username' => 'email@localhost',
            '_password' => 'random_password',
        ]);
        $client->followRedirect();

        static::assertRouteSame('runroom_user_login');
    }

    public function testItLogouts(): void
    {
        $client = static::createClient();
        $client->request('GET', '/logout');
        $client->followRedirect();

        static::assertRouteSame('runroom_user_login');
    }
}
