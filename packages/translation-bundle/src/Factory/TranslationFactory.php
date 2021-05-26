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

namespace Runroom\TranslationBundle\Factory;

use Runroom\TranslationBundle\Entity\Translation;
use Zenstruck\Foundry\ModelFactory;

/** @extends ModelFactory<Translation> */
final class TranslationFactory extends ModelFactory
{
    /** @return array<string, mixed> */
    protected function getDefaults(): array
    {
        return [
            'key' => self::faker()->unique()->word(),
            'translations' => TranslationTranslationFactory::createMany(2),
        ];
    }

    protected static function getClass(): string
    {
        return Translation::class;
    }
}
