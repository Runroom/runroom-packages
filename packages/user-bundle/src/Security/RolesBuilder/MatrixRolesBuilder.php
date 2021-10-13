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

namespace Runroom\UserBundle\Security\RolesBuilder;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @phpstan-type Role = array{
 *     role: string,
 *     role_translated: string,
 *     is_granted: boolean,
 *     label?: string,
 *     admin_label?: string
 * }
 */
final class MatrixRolesBuilder
{
    private TokenStorageInterface $tokenStorage;
    private AdminRolesBuilder $adminRolesBuilder;
    private SecurityRolesBuilder $securityRolesBuilder;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        AdminRolesBuilder $adminRolesBuilder,
        SecurityRolesBuilder $securityRolesBuilder
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->adminRolesBuilder = $adminRolesBuilder;
        $this->securityRolesBuilder = $securityRolesBuilder;
    }

    /**
     * @return array<string, array<string, string|bool>>
     *
     * @phpstan-return array<string, Role>
     */
    public function getRoles(?string $domain = null): array
    {
        if (null === $this->tokenStorage->getToken()) {
            return [];
        }

        return array_merge(
            $this->securityRolesBuilder->getRoles($domain),
            $this->adminRolesBuilder->getRoles($domain)
        );
    }

    /**
     * @return array<string, array<string, string|bool>>
     *
     * @phpstan-return array<string, Role>
     */
    public function getExpandedRoles(?string $domain = null): array
    {
        if (null === $this->tokenStorage->getToken()) {
            return [];
        }

        return array_merge(
            $this->securityRolesBuilder->getExpandedRoles($domain),
            $this->adminRolesBuilder->getRoles($domain)
        );
    }

    /** @return array<string, string> */
    public function getPermissionLabels(): array
    {
        return $this->adminRolesBuilder->getPermissionLabels();
    }
}
