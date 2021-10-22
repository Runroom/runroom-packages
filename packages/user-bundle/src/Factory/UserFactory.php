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

namespace Runroom\UserBundle\Factory;

use Runroom\UserBundle\Entity\User;
use Zenstruck\Foundry\ModelFactory;

/** @extends ModelFactory<User> */
final class UserFactory extends ModelFactory
{
    /** @return array<string, mixed> */
    protected function getDefaults(): array
    {
        return [
            'email' => static::faker()->unique()->email(),
            'password' => static::faker()->password(),
            'enabled' => static::faker()->boolean(),
            'roles' => [],
            'createdAt' => static::faker()->dateTime(),
        ];
    }

    protected static function getClass(): string
    {
        return User::class;
    }
}
