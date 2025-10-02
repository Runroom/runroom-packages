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

use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\Pool;

final readonly class GlobalVariables
{
    public function __construct(
        private Pool $pool,
        private bool $hasRequestPasswordEnabled,
    ) {}

    /**
     * @phpstan-return AdminInterface<object>
     */
    public function getUserAdmin(): AdminInterface
    {
        return $this->pool->getAdminByAdminCode('runroom.user.admin.user');
    }

    public function getHasRequestPasswordEnabled(): bool
    {
        return $this->hasRequestPasswordEnabled;
    }
}
