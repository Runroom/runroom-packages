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

use Runroom\UserBundle\Entity\ResetPasswordRequest;
use Zenstruck\Foundry\ModelFactory;

/** @extends ModelFactory<ResetPasswordRequest> */
final class ResetPasswordRequestFactory extends ModelFactory
{
    /** @return array<string, mixed> */
    protected function getDefaults(): array
    {
        return [
            'user' => UserFactory::new(),
            'selector' => self::faker()->unique()->slug(),
            'hashedToken' => self::faker()->uuid(),
            'requestedAt' => self::faker()->dateTime(),
            'expiresAt' => self::faker()->dateTime(),
        ];
    }

    protected static function getClass(): string
    {
        return ResetPasswordRequest::class;
    }
}
