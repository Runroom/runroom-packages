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

use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\SonataConfiguration;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AdminRolesBuilder implements AdminRolesBuilderInterface
{
    public function __construct(
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly Pool $pool,
        private readonly SonataConfiguration $configuration,
        private readonly TranslatorInterface $translator
    ) {}

    public function getRoles(?string $domain = null): array
    {
        $adminRoles = [];

        $adminServiceCodes = $this->pool->getAdminServiceCodes();

        foreach ($adminServiceCodes as $code) {
            $admin = $this->pool->getInstance($code);
            $baseRole = $admin->getSecurityHandler()->getBaseRole($admin);

            foreach (array_keys($admin->getSecurityInformation()) as $key) {
                $role = \sprintf($baseRole, $key);
                $adminRoles[$role] = [
                    'role' => $role,
                    'label' => $key,
                    'role_translated' => $this->translateRole($role, $domain),
                    'is_granted' => $this->isMaster($admin) || $this->authorizationChecker->isGranted($role),
                    'admin_label' => $admin->getTranslator()->trans($admin->getLabel() ?? ''),
                ];
            }
        }

        return $adminRoles;
    }

    public function getPermissionLabels(): array
    {
        $permissionLabels = [];
        foreach ($this->getRoles() as $attributes) {
            if (isset($attributes['label'])) {
                $permissionLabels[$attributes['label']] = $attributes['label'];
            }
        }

        return $permissionLabels;
    }

    /**
     * @phpstan-param AdminInterface<object> $admin
     */
    private function isMaster(AdminInterface $admin): bool
    {
        return $admin->isGranted('MASTER') || $admin->isGranted('OPERATOR')
            || $this->authorizationChecker->isGranted($this->configuration->getOption('role_super_admin'));
    }

    private function translateRole(string $role, ?string $domain = null): string
    {
        if (null !== $domain) {
            return $this->translator->trans($role, [], $domain);
        }

        return $role;
    }
}
