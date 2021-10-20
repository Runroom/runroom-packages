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

/** @phpstan-import-type Role from RolesBuilderInterface */
interface ExpandableRolesBuilderInterface extends RolesBuilderInterface
{
    /**
     * @return array<string, array<string, string|bool>>
     *
     * @phpstan-return array<string, Role>
     */
    public function getExpandedRoles(?string $domain = null): array;
}
