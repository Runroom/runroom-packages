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

class ResetPasswordRequestControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    /** @test */
    public function itRendersResetPasswordRequest(): void
    {
        $client = static::createClient();

        $client->request('GET', '/reset-password');

        static::assertResponseIsSuccessful();
    }

    /** @test */
    public function itSubmitsResetPasswordRequestWithNonExistentUser(): void
    {
        $client = static::createClient();

        $client->request('GET', '/reset-password');
        $client->submitForm('submit', [
            'reset_password_request_form[identifier]' => 'email@localhost.com',
        ]);

        static::assertEmailCount(0);

        $client->followRedirect();

        static::assertResponseIsSuccessful();
        static::assertRouteSame('runroom_user_check_email');
    }

    /** @test */
    public function itSubmitsResetPasswordRequest(): void
    {
        $client = static::createClient();

        UserFactory::createOne([
            'email' => 'email@localhost',
            'enabled' => true,
        ]);

        $client->request('GET', '/reset-password');
        $client->submitForm('submit', [
            'reset_password_request_form[identifier]' => 'email@localhost',
        ]);

        static::assertEmailCount(1);

        $client->followRedirect();

        static::assertResponseIsSuccessful();
        static::assertRouteSame('runroom_user_check_email');
    }
}
