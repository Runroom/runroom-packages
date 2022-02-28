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

namespace Runroom\BasicPageBundle\Factory;

use Runroom\BasicPageBundle\Entity\BasicPageTranslation;
use Zenstruck\Foundry\ModelFactory;

/** @extends ModelFactory<BasicPageTranslation> */
final class BasicPageTranslationFactory extends ModelFactory
{
    /**
     * @return array<string, mixed>
     */
    protected function getDefaults(): array
    {
        return [
            'title' => self::faker()->words(3, true),
            'content' => self::faker()->paragraph(),
            'slug' => self::faker()->unique(true)->slug(),
            'locale' => self::faker()->unique(true)->languageCode(),
        ];
    }

    protected static function getClass(): string
    {
        return BasicPageTranslation::class;
    }
}
