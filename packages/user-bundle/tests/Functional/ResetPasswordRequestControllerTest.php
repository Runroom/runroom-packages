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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticatorManager;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ResetPasswordRequestControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    /** @test */
    public function itSubmitsResetPasswordRequestWithNonExistentUser(): void
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

    /** @test */
    public function itSubmitsResetPasswordRequest(): void
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

    /** @test */
    public function itResetsPassword(): void
    {
        $client = static::createClient();

        $tokenGenerator = static::$container->get('symfonycasts.reset_password.token_generator');

        /** @todo: Simplify this when dropping support for Symfony 4 */
        $passwordHasher = static::$container->get(class_exists(AuthenticatorManager::class) ? 'security.password_hasher' : 'security.password_encoder');
        \assert($passwordHasher instanceof UserPasswordHasherInterface || $passwordHasher instanceof UserPasswordEncoderInterface);

        /** @phpstan-var Proxy<UserInterface> */
        $user = UserFactory::createOne([
            'email' => 'email@localhost',
            'enabled' => true,
        ])->enableAutoRefresh();

        $expiresAt = new \DateTimeImmutable(sprintf('+%d seconds', 3600));
        $tokenComponents = $tokenGenerator->createToken($expiresAt, (string) $user->object()->getId());

        ResetPasswordRequestFactory::createOne([
            'user' => $user->object(),
            'expiresAt' => $expiresAt,
            'selector' => $tokenComponents->getSelector(),
            'hashedToken' => $tokenComponents->getHashedToken(),
        ]);

        static::assertTrue($passwordHasher->isPasswordValid($user->object(), '1234'));

        $client->request('GET', sprintf('/reset-password/reset/%s', $tokenComponents->getPublicToken()));
        $client->followRedirect();

        static::assertRouteSame('runroom_user_reset_password');

        $client->submitForm('submit', [
            'change_password_form[plainPassword][first]' => 'new_password',
            'change_password_form[plainPassword][second]' => 'new_password',
        ]);
        $client->followRedirect();

        static::assertRouteSame('sonata_admin_dashboard');
        static::assertTrue($passwordHasher->isPasswordValid($user->object(), 'new_password'));
    }
}
