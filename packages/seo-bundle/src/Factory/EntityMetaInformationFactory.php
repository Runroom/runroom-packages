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
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<EntityMetaInformation>
 *
 * @method EntityMetaInformationFactory with(array|callable $attributes = [])
 */
final class EntityMetaInformationFactory extends PersistentObjectFactory
{
    /**
     * @param string[]             $locales
     * @param array<string, mixed> $defaultAttributes
     */
    public function withTranslations(array $locales, array $defaultAttributes = []): self
    {
        return $this->with([
            'translations' => EntityMetaInformationTranslationFactory::new(static function () use (&$locales, $defaultAttributes): array {
                return [...$defaultAttributes, 'locale' => array_pop($locales)];
            })->many(\count($locales)),
        ]);
    }

    public static function class(): string
    {
        return EntityMetaInformation::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [];
    }
}
