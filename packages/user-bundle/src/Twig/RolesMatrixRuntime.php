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

namespace Runroom\UserBundle\Twig;

use Runroom\UserBundle\Security\RolesBuilder\MatrixRolesBuilderInterface;
use Symfony\Component\Form\FormView;
use Twig\Environment;
use Twig\Extension\RuntimeExtensionInterface;

final readonly class RolesMatrixRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private Environment $twig,
        private MatrixRolesBuilderInterface $rolesBuilder,
    ) {}

    public function renderRolesList(FormView $form): string
    {
        $roles = $this->rolesBuilder->getRoles();
        foreach ($roles as $role => $attributes) {
            if (isset($attributes['admin_label'])) {
                unset($roles[$role]);
                continue;
            }

            $roles[$role] = $attributes;
            foreach ($form->getIterator() as $child) {
                /**
                 * @psalm-suppress PossiblyNullArrayAccess, PossiblyNullPropertyFetch
                 */
                if ($child->vars['value'] === $role) {
                    $roles[$role]['form'] = $child;
                }
            }
        }

        return $this->twig->render('@RunroomUser/admin/roles_matrix_list.html.twig', [
            'roles' => $roles,
        ]);
    }

    public function renderMatrix(FormView $form): string
    {
        $groupedRoles = [];
        foreach ($this->rolesBuilder->getRoles() as $role => $attributes) {
            if (!isset($attributes['admin_label'])) {
                continue;
            }

            $groupedRoles[$attributes['admin_label']][$role] = $attributes;
            foreach ($form->getIterator() as $child) {
                /**
                 * @psalm-suppress PossiblyNullArrayAccess, PossiblyNullPropertyFetch
                 */
                if ($child->vars['value'] === $role) {
                    $groupedRoles[$attributes['admin_label']][$role]['form'] = $child;
                }
            }
        }

        return $this->twig->render('@RunroomUser/admin/roles_matrix.html.twig', [
            'grouped_roles' => $groupedRoles,
            'permission_labels' => $this->rolesBuilder->getPermissionLabels(),
        ]);
    }
}
