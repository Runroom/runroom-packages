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

use Runroom\SeoBundle\Entity\EntityMetaInformation;
use Zenstruck\Foundry\ModelFactory;

final class EntityMetaInformationFactory extends ModelFactory
{
    /** @return array<string, mixed> */
    protected function getDefaults(): array
    {
        return [
            'translations' => EntityMetaInformationTranslationFactory::createMany(2),
        ];
    }

    protected static function getClass(): string
    {
        return EntityMetaInformation::class;
    }
}
