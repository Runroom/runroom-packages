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

namespace Runroom\UserBundle\Repository;

use Runroom\UserBundle\Model\UserInterface;

interface UserRepositoryInterface
{
    public function loadUserByIdentifier(string $identifier): ?UserInterface;

    public function create(): UserInterface;

    public function save(UserInterface $user): void;
}
