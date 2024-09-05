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

namespace Runroom\UserBundle\Tests\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Runroom\UserBundle\Entity\User;
use Runroom\UserBundle\Security\UserAuthenticator;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\SecurityRequestAttributes;

final class UserAuthenticatorTest extends TestCase
{
    /**
     * @var MockObject&UrlGeneratorInterface
     */
    private MockObject $urlGenerator;

    private Session $session;
    private string $firewallName;
    private UserAuthenticator $userAuthenticator;

    protected function setUp(): void
    {
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->session = new Session(new MockArraySessionStorage());
        $this->firewallName = 'fireWallName';

        $this->userAuthenticator = new UserAuthenticator($this->urlGenerator);
    }

    public function testItCanAuthenticateWithRequest(): void
    {
        $request = new Request([], [
            '_username' => 'username',
            '_password' => 'password',
        ]);

        $request->setSession($this->session);

        $passport = $this->userAuthenticator->authenticate($request);

        $userBadge = $passport->getBadge(UserBadge::class);
        $passwordCredential = $passport->getBadge(PasswordCredentials::class);

        static::assertInstanceOf(UserBadge::class, $userBadge);
        static::assertInstanceOf(PasswordCredentials::class, $passwordCredential);
        static::assertSame('username', $userBadge->getUserIdentifier());
        static::assertSame('password', $passwordCredential->getPassword());
        /**
         * @psalm-suppress DeprecatedClass
         *
         * @todo: Remove this conditional when dropping support for Symfony <6.2
         */
        static::assertSame('username', $request->getSession()->get(
            class_exists(SecurityRequestAttributes::class) ?
            SecurityRequestAttributes::LAST_USERNAME :
            Security::LAST_USERNAME
        ));
    }

    public function testItRedirectsWhenAuthenticationIsSuccess(): void
    {
        $request = new Request();
        $request->setSession($this->session);

        $user = new User();
        $user->setEmail('username@localhost.com');
        $user->setPassword('password');

        $token = new UsernamePasswordToken($user, $this->firewallName);

        $this->urlGenerator->method('generate')->willReturn('sonata_admin_dashboard');

        $response = $this->userAuthenticator->onAuthenticationSuccess($request, $token, $this->firewallName);

        static::assertNotNull($response);
        static::assertInstanceOf(RedirectResponse::class, $response);
    }

    public function testItRedirectsWhenAuthenticationIsNotSuccess(): void
    {
        $this->session->set('_security.' . $this->firewallName . '.target_path', 'targetValue');
        $request = new Request();
        $request->setSession($this->session);

        $user = new User();
        $user->setEmail('username@localhost.com');
        $user->setPassword('password');

        $token = new UsernamePasswordToken($user, $this->firewallName);

        $response = $this->userAuthenticator->onAuthenticationSuccess($request, $token, $this->firewallName);

        static::assertNotNull($response);
        static::assertInstanceOf(RedirectResponse::class, $response);
        static::assertSame('targetValue', $response->getTargetUrl());
    }
}
