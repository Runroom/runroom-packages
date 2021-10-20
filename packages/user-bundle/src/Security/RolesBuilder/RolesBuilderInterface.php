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

/**
 * @phpstan-type Role = array{
 *     role: string,
 *     role_translated: string,
 *     is_granted: boolean,
 *     label?: string,
 *     admin_label?: string
 * }
 */
interface RolesBuilderInterface
{
    /**
     * @return array<string, array<string, string|bool>>
     *
     * @phpstan-return array<string, Role>
     */
    public function getRoles(?string $domain = null): array;
}
