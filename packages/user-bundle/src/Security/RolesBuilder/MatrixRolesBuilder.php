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

final class MatrixRolesBuilder implements MatrixRolesBuilderInterface
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly AdminRolesBuilderInterface $adminRolesBuilder,
        private readonly ExpandableRolesBuilderInterface $securityRolesBuilder
    ) {
    }

    public function getRoles(?string $domain = null): array
    {
        if (null === $this->tokenStorage->getToken()) {
            return [];
        }

        return [...$this->securityRolesBuilder->getRoles($domain), ...$this->adminRolesBuilder->getRoles($domain)];
    }

    public function getExpandedRoles(?string $domain = null): array
    {
        if (null === $this->tokenStorage->getToken()) {
            return [];
        }

        return [...$this->securityRolesBuilder->getExpandedRoles($domain), ...$this->adminRolesBuilder->getRoles($domain)];
    }

    public function getPermissionLabels(): array
    {
        return $this->adminRolesBuilder->getPermissionLabels();
    }
}
