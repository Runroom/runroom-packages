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

namespace Runroom\CookiesBundle\Factory;

use Runroom\CookiesBundle\Entity\CookiesPageTranslation;
use Zenstruck\Foundry\ModelFactory;

final class CookiesPageTranslationFactory extends ModelFactory
{
    /** @return array<string, mixed> */
    protected function getDefaults(): array
    {
        return [
            'title' => self::faker()->words(3, true),
            'content' => self::faker()->paragraph(),
            'locale' => self::faker()->unique()->languageCode()
        ];
    }

    protected static function getClass(): string
    {
        return CookiesPageTranslation::class;
    }
}
