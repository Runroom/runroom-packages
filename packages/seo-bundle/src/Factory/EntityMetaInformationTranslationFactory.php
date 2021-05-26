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

namespace Runroom\SeoBundle\Factory;

use Runroom\SeoBundle\Entity\EntityMetaInformationTranslation;
use Zenstruck\Foundry\ModelFactory;

final class EntityMetaInformationTranslationFactory extends ModelFactory
{
    /** @return array<string, mixed> */
    protected function getDefaults(): array
    {
        return [
            'title' => self::faker()->words(3, true),
            'description' => self::faker()->paragraph(),
            'locale' => self::faker()->unique()->languageCode(),
        ];
    }

    protected static function getClass(): string
    {
        return EntityMetaInformationTranslation::class;
    }
}
