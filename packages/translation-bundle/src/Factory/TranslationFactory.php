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
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Translation>
 */
final class TranslationFactory extends PersistentObjectFactory
{
    /**
     * @param string[]             $locales
     * @param array<string, mixed> $defaultAttributes
     */
    public function withTranslations(array $locales, array $defaultAttributes = []): self
    {
        return $this->with([
            'translations' => TranslationTranslationFactory::new(static function () use (&$locales, $defaultAttributes): array {
                return [...$defaultAttributes, 'locale' => array_pop($locales)];
            })->many(\count($locales)),
        ]);
    }

    public static function class(): string
    {
        return Translation::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'key' => self::faker()->unique()->word(),
        ];
    }
}
