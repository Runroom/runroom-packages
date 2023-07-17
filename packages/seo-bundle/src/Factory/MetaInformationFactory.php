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

/**
 * @extends ModelFactory<MetaInformation>
 *
 * @method MetaInformationFactory addState(array|callable $attributes = [])
 */
final class MetaInformationFactory extends ModelFactory
{
    /**
     * @param string[]             $locales
     * @param array<string, mixed> $defaultAttributes
     */
    public function withTranslations(array $locales, array $defaultAttributes = []): self
    {
        return $this->addState([
            'translations' => MetaInformationTranslationFactory::new(static function () use (&$locales, $defaultAttributes): array {
                return [...$defaultAttributes, 'locale' => array_pop($locales)];
            })->many(\count($locales)),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDefaults(): array
    {
        return [
            'route' => self::faker()->unique()->word(),
            'routeName' => self::faker()->words(3, true),
        ];
    }

    protected static function getClass(): string
    {
        return MetaInformation::class;
    }
}
