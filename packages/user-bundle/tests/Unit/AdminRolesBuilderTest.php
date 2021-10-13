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
        $this->admin = $this->createMock(AdminInterface::class);

        $container = new Container();
        $container->set('runroom_user.admin.user', $this->admin);

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
            'list_action_button_content' => 'content',
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
            'use_bootl' => true,
            'use_icheck' => true,
            'use_select2' => true,
            'use_stickyforms' => true,
        ]);

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

        $expected = [
            'GUEST' => 'GUEST',
            'STAFF' => 'STAFF',
            'EDITOR' => 'EDITOR',
            'ADMIN' => 'ADMIN',
        ];

        static::assertSame($expected, $this->rolesBuilder->getPermissionLabels());
    }
}
