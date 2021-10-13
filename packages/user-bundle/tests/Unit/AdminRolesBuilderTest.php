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
use Runroom\UserBundle\Security\RolesBuilder\AdminRolesBuilder;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Security\Handler\SecurityHandlerInterface;
use Sonata\AdminBundle\SonataConfiguration;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\Translator;

class AdminRolesBuilderTest extends TestCase
{
    /** @var MockObject&AdminInterface */
    private MockObject $admin;

    private AdminRolesBuilder $rolesBuilder;

    protected function setUp(): void
    {
        $this->admin = $this->createMock(AdminInterface::class);

        $container = new Container();
        $container->set('runroom_user.admin.user', $this->admin);

        $this->rolesBuilder = new AdminRolesBuilder(
            $this->createStub(AuthorizationCheckerInterface::class),
            new Pool($container, ['runroom_user.admin.user']),
            new SonataConfiguration('title', 'logo', []),
            new Translator('en')
        );
    }

    /** @test */
    public function itGetsPermissionLabels(): void
    {
        $securityHandler = $this->createMock(SecurityHandlerInterface::class);
        $securityHandler->method('getBaseRole')->willReturn('ROLE_SONATA_FOO_%s');

        $this->admin->method('getSecurityHandler')->willReturn($securityHandler);
        $this->admin->method('getSecurityInformation')->willReturn([
            'GUEST' => ['VIEW', 'LIST'],
            'STAFF' => ['EDIT', 'LIST', 'CREATE'],
            'EDITOR' => ['OPERATOR', 'EXPORT'],
            'ADMIN' => ['MASTER'],
        ]);

        $expected = [
            'GUEST' => 'GUEST',
            'STAFF' => 'STAFF',
            'EDITOR' => 'EDITOR',
            'ADMIN' => 'ADMIN',
        ];

        static::assertSame($expected, $this->rolesBuilder->getPermissionLabels());
    }
}
