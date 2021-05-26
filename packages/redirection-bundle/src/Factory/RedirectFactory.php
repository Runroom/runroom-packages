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

namespace Runroom\RedirectionBundle\Factory;

use Runroom\RedirectionBundle\Entity\Redirect;
use Zenstruck\Foundry\ModelFactory;

final class RedirectFactory extends ModelFactory
{
    /** @return array<string, mixed> */
    protected function getDefaults(): array
    {
        $uniqueUrl = self::faker()->unique();

        return [
            'source' => $uniqueUrl->url(),
            'destination' => $uniqueUrl->url(),
            'httpCode' => self::faker()->randomElement([
                Redirect::PERMANENT,
                Redirect::TEMPORAL,
            ]),
            'automatic' => self::faker()->boolean(),
            'publish' => self::faker()->boolean(),
        ];
    }

    protected static function getClass(): string
    {
        return Redirect::class;
    }
}
