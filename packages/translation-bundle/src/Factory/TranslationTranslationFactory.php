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

use Runroom\TranslationBundle\Entity\TranslationTranslation;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<TranslationTranslation>
 */
final class TranslationTranslationFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return TranslationTranslation::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'value' => self::faker()->words(3, true),
            'locale' => self::faker()->unique(true)->languageCode(),
        ];
    }
}
