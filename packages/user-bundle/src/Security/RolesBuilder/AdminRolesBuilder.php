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
    private AuthorizationCheckerInterface $authorizationChecker;
    private Pool $pool;
    private SonataConfiguration $configuration;
    private TranslatorInterface $translator;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        Pool $pool,
        SonataConfiguration $configuration,
        TranslatorInterface $translator
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->pool = $pool;
        $this->configuration = $configuration;
        $this->translator = $translator;
    }

    public function getRoles(?string $domain = null): array
    {
        $adminRoles = [];
        foreach ($this->pool->getAdminServiceIds() as $id) {
            $admin = $this->pool->getInstance($id);
            $baseRole = $admin->getSecurityHandler()->getBaseRole($admin);

            foreach (array_keys($admin->getSecurityInformation()) as $key) {
                $role = sprintf($baseRole, $key);
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

    /** @phpstan-param AdminInterface<object> $admin */
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
