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
use Runroom\UserBundle\Security\UserAuthenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class UserAuthenticatorTest extends TestCase
{
    /* @var MockObject&UrlGeneratorInterface **/
    private MockObject $urlGenerator;
    private UserAuthenticator $userAuthenticator;
    private Session $session;

    protected function setUp(): void
    {
        /* @todo: Simplify this when dropping support for Symfony 4 */
        if (!class_exists(AbstractLoginFormAuthenticator::class)) {
            static::markTestSkipped('Only works with SF 5.1 or higher');
        }

        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->userAuthenticator = new UserAuthenticator($this->urlGenerator);
        $this->session = new Session(new MockArraySessionStorage());
    }

    /** @test */
    public function itCanAuthenticateWithRequest(): void
    {
        $request = new Request([], [
            '_username' => 'username',
            '_password' => 'secret',
        ]);
        $request->setSession($this->session);

        $passport = $this->userAuthenticator->authenticate($request);

        $userBadge = $passport->getBadge(UserBadge::class);
        $passwordCredential = $passport->getBadge(PasswordCredentials::class);

        static::assertInstanceOf(UserBadge::class, $userBadge);
        static::assertInstanceOf(PasswordCredentials::class, $passwordCredential);
        static::assertSame('username', $userBadge->getUserIdentifier());
        static::assertSame('secret', $passwordCredential->getPassword());
        static::assertSame('username', $request->getSession()->get(Security::LAST_USERNAME));
    }

    /** @test */
    public function itRedirectsWhenAuthenticationIsSuccess(): void
    {
        $request = new Request();
        $request->setSession($this->session);
        $token = new UsernamePasswordToken('username', 'secret', 'firewallName');
        $this->urlGenerator->method('generate')->willReturn('sonata_admin_dashboard');

        $response = $this->userAuthenticator->onAuthenticationSuccess($request, $token, 'firewallName');

        static::assertNotNull($response);
        static::assertInstanceOf(RedirectResponse::class, $response);
    }
}
