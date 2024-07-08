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

use Runroom\UserBundle\Factory\ResetPasswordRequestFactory;
use Runroom\UserBundle\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

use function Zenstruck\Foundry\Persistence\refresh;
use function Zenstruck\Foundry\Persistence\proxy;

final class ResetPasswordRequestControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testItSubmitsResetPasswordRequestWithNonExistentUser(): void
    {
        $client = static::createClient();

        $client->request('GET', '/reset-password');

        static::assertResponseIsSuccessful();

        $client->submitForm('submit', [
            'reset_password_request_form[identifier]' => 'email@localhost.com',
        ]);

        static::assertEmailCount(0);

        $client->followRedirect();

        static::assertResponseIsSuccessful();
        static::assertRouteSame('runroom_user_check_email');
    }

    public function testItSubmitsResetPasswordRequest(): void
    {
        $client = static::createClient();

        UserFactory::createOne([
            'email' => 'email@localhost',
            'enabled' => true,
        ]);

        $client->request('GET', '/reset-password');

        static::assertResponseIsSuccessful();

        $client->submitForm('submit', [
            'reset_password_request_form[identifier]' => 'email@localhost',
        ]);

        static::assertEmailCount(1);

        $client->followRedirect();

        static::assertResponseIsSuccessful();
        static::assertRouteSame('runroom_user_check_email');
    }

    public function testItThrows404IfTryToResetPasswordWithoutToken(): void
    {
        $client = static::createClient();
        $client->catchExceptions(true);
        $client->request('GET', '/reset-password/reset');

        static::assertResponseStatusCodeSame(404);
    }

    public function testItRedirectsToResetPasswordRequestOnInvalidToken(): void
    {
        $client = static::createClient();
        $client->followRedirects(true);
        $client->request('GET', '/reset-password/reset/25');

        static::assertRouteSame('runroom_user_forgot_password_request');
    }

    /**
     * @see We need to use the internal token generator to generate a valid token for testing purposes
     *
     * @psalm-suppress InternalMethod
     */
    public function testItResetsPassword(): void
    {
        $client = static::createClient();

        $tokenGenerator = static::getContainer()->get('symfonycasts.reset_password.token_generator');

        $user = UserFactory::createOne([
            'email' => 'email@localhost',
            'enabled' => true,
            'password' => '1234',
        ]);

        $expiresAt = new \DateTimeImmutable(sprintf('+%d seconds', 3600));
        $tokenComponents = $tokenGenerator->createToken($expiresAt, (string) $user->getId());

        ResetPasswordRequestFactory::createOne([
            'user' => $user,
            'expiresAt' => $expiresAt,
            'selector' => $tokenComponents->getSelector(),
            'hashedToken' => $tokenComponents->getHashedToken(),
        ]);

        static::assertSame($user->getPassword(), '1234');

        $client->request('GET', sprintf('/reset-password/reset/%s', $tokenComponents->getPublicToken()));
        $client->followRedirect();

        static::assertRouteSame('runroom_user_reset_password');

        $client->submitForm('submit', [
            'change_password_form[plainPassword][first]' => 'new_password',
            'change_password_form[plainPassword][second]' => 'new_password',
        ]);
        $client->followRedirect();

        // @TODO: Remove else when dropping support for zenstruct/foundry 1
        if (\function_exists('Zenstruck\Foundry\Persistence\refresh')) {
            refresh($user);
        } else {
            $user = proxy($user)->_refresh()->_real();
        }

        static::assertRouteSame('sonata_admin_dashboard');
        static::assertSame($user->getPassword(), 'new_password');
    }
}
