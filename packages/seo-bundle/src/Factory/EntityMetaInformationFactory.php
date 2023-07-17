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

/**
 * @extends ModelFactory<EntityMetaInformation>
 *
 * @method EntityMetaInformationFactory addState(array|callable $attributes = [])
 */
final class EntityMetaInformationFactory extends ModelFactory
{
    /**
     * @param string[]             $locales
     * @param array<string, mixed> $defaultAttributes
     */
    public function withTranslations(array $locales, array $defaultAttributes = []): self
    {
        return $this->addState([
            'translations' => EntityMetaInformationTranslationFactory::new(static function () use (&$locales, $defaultAttributes): array {
                return [...$defaultAttributes, 'locale' => array_pop($locales)];
            })->many(\count($locales)),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDefaults(): array
    {
        return [];
    }

    protected static function getClass(): string
    {
        return EntityMetaInformation::class;
    }
}
