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

namespace Runroom\BasicPageBundle\Factory;

use Runroom\BasicPageBundle\Entity\BasicPage;
use Runroom\SeoBundle\Factory\EntityMetaInformationFactory;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<BasicPage>
 */
final class BasicPageFactory extends PersistentObjectFactory
{
    /**
     * @param string[]             $locales
     * @param array<string, mixed> $defaultAttributes
     */
    public function withTranslations(array $locales, array $defaultAttributes = []): self
    {
        return $this->with([
            'translations' => BasicPageTranslationFactory::new(static function () use (&$locales, $defaultAttributes): array {
                return [...$defaultAttributes, 'locale' => array_pop($locales)];
            })->many(\count($locales)),
        ]);
    }

    public static function class(): string
    {
        return BasicPage::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'location' => self::faker()->randomElement([
                BasicPage::LOCATION_FOOTER,
                BasicPage::LOCATION_NONE,
            ]),
            'publish' => self::faker()->boolean(),
            'metaInformation' => EntityMetaInformationFactory::new(),
        ];
    }
}
