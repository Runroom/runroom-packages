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
    /**
     * @var MockObject&AdminInterface
     *
     * @phpstan-var MockObject&AdminInterface<object>
     */
    private MockObject $admin;

    private AdminRolesBuilder $rolesBuilder;

    protected function setUp(): void
    {
        $translator = new Translator('en');
        $sonataConfiguration = new SonataConfiguration('title', 'logo', [
            'confirm_exit' => true,
            'default_group' => 'group',
            'default_icon' => 'icon',
            'default_label_catalogue' => 'label_catalogue',
            'dropdown_number_groups_per_colums' => 1,
            'form_type' => 'type',
            'html5_validate' => true,
            'javascripts' => [],
            'js_debug' => true,
            'list_action_button_content' => 'text',
            'lock_protection' => true,
            'logo_content' => 'text',
            'mosaic_background' => 'background',
            'pager_links' => 1,
            'role_admin' => 'ROLE_ADMIN',
            'role_super_admin' => 'ROLE_SUPER_ADMIN',
            'search' => true,
            'skin' => 'blue',
            'sort_admins' => true,
            'stylesheets' => [],
            'use_bootlint' => true,
            'use_icheck' => true,
            'use_select2' => true,
            'use_stickyforms' => true,
        ]);

        $this->admin = $this->createMock(AdminInterface::class);
        $this->admin->method('getTranslator')->willReturn($translator);

        $container = new Container();
        $container->set('runroom_user.admin.user', $this->admin);

        $this->rolesBuilder = new AdminRolesBuilder(
            $this->createStub(AuthorizationCheckerInterface::class),
            new Pool($container, ['runroom_user.admin.user']),
            $sonataConfiguration,
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

        $permissionLabels = $this->rolesBuilder->getPermissionLabels();

        static::assertSame([
            'GUEST' => 'GUEST',
            'STAFF' => 'STAFF',
            'EDITOR' => 'EDITOR',
            'ADMIN' => 'ADMIN',
        ], $permissionLabels);
    }

    /** @test */
    public function itGetsRoles(): void
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

        $roles = $this->rolesBuilder->getRoles('domain');

        $expectedRoles = [
            'ROLE_SONATA_FOO_GUEST' => [
                'role' => 'ROLE_SONATA_FOO_GUEST',
                'label' => 'GUEST',
                'role_translated' => 'ROLE_SONATA_FOO_GUEST',
                'is_granted' => false,
                'admin_label' => '',
            ],
            'ROLE_SONATA_FOO_STAFF' => [
                'role' => 'ROLE_SONATA_FOO_STAFF',
                'label' => 'STAFF',
                'role_translated' => 'ROLE_SONATA_FOO_STAFF',
                'is_granted' => false,
                'admin_label' => '',
            ],
            'ROLE_SONATA_FOO_EDITOR' => [
                'role' => 'ROLE_SONATA_FOO_EDITOR',
                'label' => 'EDITOR',
                'role_translated' => 'ROLE_SONATA_FOO_EDITOR',
                'is_granted' => false,
                'admin_label' => '',
            ],
            'ROLE_SONATA_FOO_ADMIN' => [
                'role' => 'ROLE_SONATA_FOO_ADMIN',
                'label' => 'ADMIN',
                'role_translated' => 'ROLE_SONATA_FOO_ADMIN',
                'is_granted' => false,
                'admin_label' => '',
            ],
        ];

        static::assertSame($expectedRoles, $roles);
    }
}
