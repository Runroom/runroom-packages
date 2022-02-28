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
use Prophecy\Argument\Token\TokenInterface;
use Runroom\UserBundle\Security\RolesBuilder\AdminRolesBuilderInterface;
use Runroom\UserBundle\Security\RolesBuilder\ExpandableRolesBuilderInterface;
use Runroom\UserBundle\Security\RolesBuilder\MatrixRolesBuilder;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MatrixRolesBuilderTest extends TestCase
{
    /** @var Stub&TokenStorageInterface */
    private Stub $tokenStorage;

    /** @var Stub&AdminRolesBuilderInterface */
    private Stub $adminRolesBuilder;

    /** @var Stub&ExpandableRolesBuilderInterface */
    private Stub $expandableRolesBuilder;

    /** @var array<string, array<string, string|bool>> */
    private array $adminRole;

    /** @var array<string, array<string, string|bool>> */
    private array $guestRole;

    private MatrixRolesBuilder $matrixRolesBuilder;

    protected function setUp(): void
    {
        $this->tokenStorage = $this->createStub(TokenStorageInterface::class);
        $this->adminRolesBuilder = $this->createStub(AdminRolesBuilderInterface::class);
        $this->expandableRolesBuilder = $this->createStub(ExpandableRolesBuilderInterface::class);

        $this->adminRole = ['ROLE_SONATA_FOO_ADMIN' => [
            'role' => 'ROLE_SONATA_FOO_ADMIN',
            'label' => 'ADMIN',
            'role_translated' => 'ROLE_SONATA_FOO_ADMIN',
            'is_granted' => false,
            'admin_label' => '',
        ]];
        $this->guestRole = ['ROLE_SONATA_FOO_GUEST' => [
            'role' => 'ROLE_SONATA_FOO_GUEST',
            'label' => 'GUEST',
            'role_translated' => 'ROLE_SONATA_FOO_GUEST',
            'is_granted' => false,
            'admin_label' => '',
        ]];

        $this->matrixRolesBuilder = new MatrixRolesBuilder(
            $this->tokenStorage,
            $this->adminRolesBuilder,
            $this->expandableRolesBuilder
        );
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
        $this->tokenStorage->method('getToken')->willReturn($this->createStub(TokenInterface::class));
        $this->adminRolesBuilder->method('getRoles')->willReturn($this->adminRole);
        $this->expandableRolesBuilder->method('getRoles')->willReturn($this->guestRole);
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
        $this->tokenStorage->method('getToken')->willReturn($this->createStub(TokenInterface::class));
        $this->adminRolesBuilder->method('getRoles')->willReturn($this->adminRole);
        $this->expandableRolesBuilder->method('getExpandedRoles')->willReturn($this->guestRole);
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
