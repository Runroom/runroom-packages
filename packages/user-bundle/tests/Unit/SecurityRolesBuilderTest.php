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

use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Runroom\UserBundle\Security\RolesBuilder\SecurityRolesBuilder;
use Sonata\AdminBundle\SonataConfiguration;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\Translator;

class SecurityRolesBuilderTest extends TestCase
{
    /** @var Stub&AuthorizationCheckerInterface */
    private Stub $authorizationChecker;

    private SecurityRolesBuilder $securityRolesBuilder;

    protected function setUp(): void
    {
        $this->authorizationChecker = $this->createStub(AuthorizationCheckerInterface::class);

        $sonataConfiguration = new SonataConfiguration('title', 'logo', [
            'confirm_exit' => true,
            'default_admin_route' => 'edit',
            'default_group' => 'group',
            'default_icon' => 'icon',
            'default_translation_domain' => 'label_catalogue',
            'dropdown_number_groups_per_colums' => 1,
            'form_type' => 'standard',
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
            'skin' => 'skin-blue',
            'sort_admins' => true,
            'stylesheets' => [],
            'use_bootlint' => true,
            'use_icheck' => true,
            'use_select2' => true,
            'use_stickyforms' => true,
        ]);

        $this->securityRolesBuilder = new SecurityRolesBuilder(
            $this->authorizationChecker,
            $sonataConfiguration,
            new Translator('en'),
            [
                'ROLE_ADMIN' => ['ROLE_USER'],
                'ROLE_SUPER_ADMIN' => ['ROLE_ADMIN', 'ROLE_ALLOWED_TO_SWITCH'],
            ],
        );
    }

    /** @test */
    public function itGetsExpandedRoles(): void
    {
        $this->authorizationChecker->method('isGranted')->willReturnMap([
            ['ROLE_SUPER_ADMIN', null, true],
            ['ROLE_ALLOWED_TO_SWITCH', null, false],
            ['ROLE_ADMIN', null, true],
            ['ROLE_USER', null, false],
        ]);

        $expectedExpandedRoles = [
            'ROLE_SUPER_ADMIN' => [
              'role' => 'ROLE_SUPER_ADMIN',
              'role_translated' => 'ROLE_SUPER_ADMIN: ROLE_ADMIN, ROLE_ALLOWED_TO_SWITCH',
              'is_granted' => true,
            ],
            'ROLE_ALLOWED_TO_SWITCH' => [
              'role' => 'ROLE_ALLOWED_TO_SWITCH',
              'role_translated' => 'ROLE_ALLOWED_TO_SWITCH',
              'is_granted' => false,
            ],
            'ROLE_ADMIN' => [
              'role' => 'ROLE_ADMIN',
              'role_translated' => 'ROLE_ADMIN: ROLE_USER',
              'is_granted' => true,
            ],
            'ROLE_USER' => [
              'role' => 'ROLE_USER',
              'role_translated' => 'ROLE_USER',
              'is_granted' => false,
            ],
        ];

        $expandedRoles = $this->securityRolesBuilder->getExpandedRoles();

        static::assertSame($expectedExpandedRoles, $expandedRoles);
    }

    /** @test */
    public function itGetsRoles(): void
    {
        $this->authorizationChecker->method('isGranted')->willReturnMap([
            ['ROLE_SUPER_ADMIN', null, true],
            ['ROLE_ALLOWED_TO_SWITCH', null, false],
            ['ROLE_ADMIN', null, true],
            ['ROLE_USER', null, false],
        ]);

        $expectedRoles = [
            'ROLE_SUPER_ADMIN' => [
                'role' => 'ROLE_SUPER_ADMIN',
                'role_translated' => 'ROLE_SUPER_ADMIN',
                'is_granted' => true,
            ],
            'ROLE_ALLOWED_TO_SWITCH' => [
                'role' => 'ROLE_ALLOWED_TO_SWITCH',
                'role_translated' => 'ROLE_ALLOWED_TO_SWITCH',
                'is_granted' => false,
            ],
            'ROLE_ADMIN' => [
                'role' => 'ROLE_ADMIN',
                'role_translated' => 'ROLE_ADMIN',
                'is_granted' => true,
            ],
            'ROLE_USER' => [
                'role' => 'ROLE_USER',
                'role_translated' => 'ROLE_USER',
                'is_granted' => false,
            ],
        ];

        $roles = $this->securityRolesBuilder->getRoles('domain');

        static::assertSame($expectedRoles, $roles);
    }
}
