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
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use Zenstruck\Foundry\ModelFactory;

/** @extends ModelFactory<ResetPasswordRequestInterface> */
final class ResetPasswordRequestFactory extends ModelFactory
{
    /** @return array<string, mixed> */
    protected function getDefaults(): array
    {
        return [
            'user' => UserFactory::new(),
            'expiresAt' => \DateTimeImmutable::createFromMutable(static::faker()->dateTime()),
            'selector' => static::faker()->unique()->slug(),
            'hashedToken' => static::faker()->uuid(),
        ];
    }

    protected static function getClass(): string
    {
        return ResetPasswordRequest::class;
    }
}
