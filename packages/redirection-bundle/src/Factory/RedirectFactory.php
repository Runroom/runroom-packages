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
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Redirect>
 */
final class RedirectFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return Redirect::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array
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
}
