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
use Runroom\UserBundle\Model\UserInterface;
use Zenstruck\Foundry\ModelFactory;

/** @extends ModelFactory<UserInterface> */
final class UserFactory extends ModelFactory
{
    public const DEFAULT_PASSWORD = '1234';

    /** @return array<string, mixed> */
    protected function getDefaults(): array
    {
        return [
            'email' => static::faker()->unique()->email(),
            'password' => '$argon2id$v=19$m=65536,t=4,p=1$pLFF3D2gnvDmxMuuqH4BrA$3vKfv0cw+6EaNspq9btVAYc+jCOqrmWRstInB2fRPeQ',
            'enabled' => static::faker()->boolean(),
            'createdAt' => static::faker()->dateTime(),
        ];
    }

    protected static function getClass(): string
    {
        return User::class;
    }
}
