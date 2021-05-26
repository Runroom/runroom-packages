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

use Runroom\SeoBundle\Entity\MetaInformation;
use Zenstruck\Foundry\ModelFactory;

final class MetaInformationFactory extends ModelFactory
{
    /** @return array<string, mixed> */
    protected function getDefaults(): array
    {
        return [
            'route' => self::faker()->unique()->word(),
            'routeName' => self::faker()->words(3, true),
            'translations' => MetaInformationTranslationFactory::createMany(2),
        ];
    }

    protected static function getClass(): string
    {
        return MetaInformation::class;
    }
}
