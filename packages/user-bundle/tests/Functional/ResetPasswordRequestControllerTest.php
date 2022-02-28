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
use Runroom\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ResetPasswordRequestControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    /**
     * @test
     */
    public function itSubmitsResetPasswordRequestWithNonExistentUser(): void
    {
        $client = static::createClient();

        // @todo: Simplify when this gets solved: https://github.com/symfony/symfony/issues/45580
        $client->disableReboot();

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

    /**
     * @test
     */
    public function itSubmitsResetPasswordRequest(): void
    {
        $client = static::createClient();

        // @todo: Simplify when this gets solved: https://github.com/symfony/symfony/issues/45580
        $client->disableReboot();

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

    /**
     * @test
     */
    public function itThrows404IfTryToResetPasswordWithoutToken(): void
    {
        $client = static::createClient();
        $client->catchExceptions(true);
        $client->request('GET', '/reset-password/reset');

        static::assertResponseStatusCodeSame(404);
    }

    /**
     * @test
     */
    public function itRedirectsToResetPasswordRequestOnInvalidToken(): void
    {
        $client = static::createClient();
        $client->followRedirects(true);
        $client->request('GET', '/reset-password/reset/25');

        static::assertRouteSame('runroom_user_forgot_password_request');
    }

    /**
     * @test
     *
     * @see We need to use the internal token generator to generate a valid token for testing purposes
     */
    public function itResetsPassword(): void
    {
        $client = static::createClient();

        /**
         * @todo: Simplify this when dropping support for Symfony 4
         */
        $container = method_exists(static::class, 'getContainer') ? static::getContainer() : static::$container;

        $tokenGenerator = $container->get('symfonycasts.reset_password.token_generator');

        /** @phpstan-var Proxy<UserInterface> */
        $user = UserFactory::createOne([
            'email' => 'email@localhost',
            'enabled' => true,
            'password' => '1234',
        ])->enableAutoRefresh();

        $expiresAt = new \DateTimeImmutable(sprintf('+%d seconds', 3600));
        $tokenComponents = $tokenGenerator->createToken($expiresAt, (string) $user->getId());

        ResetPasswordRequestFactory::createOne([
            'user' => $user->object(),
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

        static::assertRouteSame('sonata_admin_dashboard');
        static::assertSame($user->getPassword(), 'new_password');
    }
}
