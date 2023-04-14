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

/**
 * @extends ModelFactory<Translation>
 *
 * @method TranslationFactory addState(array|callable $attributes = [])
 */
final class TranslationFactory extends ModelFactory
{
    /**
     * @param string[]             $locales
     * @param array<string, mixed> $defaultAttributes
     */
    public function withTranslations(array $locales, array $defaultAttributes = []): self
    {
        return $this->addState([
            'translations' => TranslationTranslationFactory::new(static function () use (&$locales, $defaultAttributes): array {
                return array_merge($defaultAttributes, ['locale' => array_pop($locales)]);
            })->many(\count($locales)),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDefaults(): array
    {
        return [
            'key' => self::faker()->unique()->word(),
        ];
    }

    protected static function getClass(): string
    {
        return Translation::class;
    }
}
