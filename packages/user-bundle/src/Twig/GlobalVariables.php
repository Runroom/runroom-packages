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

final class GlobalVariables
{
    private Pool $pool;

    private bool $hasRequestPasswordEnabled;

    public function __construct(Pool $pool, bool $hasRequestPasswordEnabled)
    {
        $this->pool = $pool;
        $this->hasRequestPasswordEnabled = $hasRequestPasswordEnabled;
    }

    /**
     * @phpstan-return AdminInterface<object>
     */
    public function getUserAdmin(): AdminInterface
    {
        return $this->pool->getAdminByAdminCode('runroom_user.admin.user');
    }

    public function getHasRequestPasswordEnabled(): bool
    {
        return $this->hasRequestPasswordEnabled;
    }
}
