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
use Prophecy\Argument\Token\TokenInterface;
use Runroom\UserBundle\Security\RolesBuilder\AdminRolesBuilderInterface;
use Runroom\UserBundle\Security\RolesBuilder\ExpandableRolesBuilderInterface;
use Runroom\UserBundle\Security\RolesBuilder\MatrixRolesBuilder;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MatrixRolesBuilderTest extends TestCase
{
    /** @var MockObject&TokenStorageInterface */
    private MockObject $tokenStorage;

    /** @var MockObject&AdminRolesBuilderInterface */
    private MockObject $adminRolesBuilder;

    /** @var MockObject&ExpandableRolesBuilderInterface */
    private MockObject $expandableRolesBuilderInterface;

    private MatrixRolesBuilder $matrixRolesBuilder;

    /** @var array<string, array<string, string|bool>> */
    private array $adminRole;

    /** @var array<string, array<string, string|bool>> */
    private array $guestRole;

    protected function setUp(): void
    {
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->adminRolesBuilder = $this->createMock(AdminRolesBuilderInterface::class);
        $this->expandableRolesBuilderInterface = $this->createMock(ExpandableRolesBuilderInterface::class);

        $this->matrixRolesBuilder = new MatrixRolesBuilder(
            $this->tokenStorage,
            $this->adminRolesBuilder,
            $this->expandableRolesBuilderInterface
        );

        $this->adminRole = ['ROLE_SONATA_FOO_ADMIN' => [
            'role' => 'ROLE_SONATA_FOO_ADMIN',
            'label' => 'ADMIN',
            'role_translated' => 'ROLE_SONATA_FOO_ADMIN',
            'is_granted' => false,
            'admin_label' => '', ],
        ];
        $this->guestRole = ['ROLE_SONATA_FOO_GUEST' => [
            'role' => 'ROLE_SONATA_FOO_GUEST',
            'label' => 'GUEST',
            'role_translated' => 'ROLE_SONATA_FOO_GUEST',
            'is_granted' => false,
            'admin_label' => '', ],
        ];
    }

    /** @test */
    public function itGetsEmptyArrayRoles(): void
    {
        $this->tokenStorage->method('getToken')->willReturn(null);
        $result = $this->matrixRolesBuilder->getRoles('domain');

        static::assertSame([], $result);
    }

    /** @test */
    public function itGetsArrayRoles(): void
    {
        $this->tokenStorage->method('getToken')->willReturn(TokenInterface::class);
        $this->adminRolesBuilder->method('getRoles')->willReturn($this->adminRole);
        $this->expandableRolesBuilderInterface->method('getRoles')->willReturn($this->guestRole);
        $result = $this->matrixRolesBuilder->getRoles('domain');

        static::assertSame(array_merge($this->guestRole, $this->adminRole), $result);
    }

    /** @test */
    public function itGetsEmptyExpandedArrayRoles(): void
    {
        $this->tokenStorage->method('getToken')->willReturn(null);

        $result = $this->matrixRolesBuilder->getExpandedRoles('domain');

        static::assertSame([], $result);
    }

    /** @test */
    public function itGetsExpandedArrayRoles(): void
    {
        $this->tokenStorage->method('getToken')->willReturn(TokenInterface::class);
        $this->adminRolesBuilder->method('getRoles')->willReturn($this->adminRole);
        $this->expandableRolesBuilderInterface->method('getExpandedRoles')->willReturn($this->guestRole);
        $result = $this->matrixRolesBuilder->getExpandedRoles('domain');

        static::assertSame(array_merge($this->guestRole, $this->adminRole), $result);
    }

    /** @test */
    public function itGetPermissions(): void
    {
        $this->adminRolesBuilder->method('getPermissionLabels')->willReturn(['label' => 'permission']);
        $result = $this->matrixRolesBuilder->getPermissionLabels();
        static::assertSame(['label' => 'permission'], $result);
    }
}
