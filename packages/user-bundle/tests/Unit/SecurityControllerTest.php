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

use PHPUnit\Framework\TestCase;
use Runroom\UserBundle\Controller\SecurityController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityControllerTest extends TestCase
{
    /**
     * @test
     *
     * This test is only for coverage purpose, this method is not really called
     * because it is intercepted by Symfony Security.
     */
    public function itThrowsOnLogoutBecauseItShouldNotBeExecuted(): void
    {
        $controller = new SecurityController($this->createStub(AuthenticationUtils::class));

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('This method can be blank. It will be intercepted by the logout key on your firewall.');

        $controller->logout();
    }
}
