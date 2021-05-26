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
use Zenstruck\Foundry\ModelFactory;

/** @extends ModelFactory<BasicPage> */
final class BasicPageFactory extends ModelFactory
{
    /**
     * @param string[] $locales
     * @param array<string, mixed> $defaultAttributes
     */
    public function withTranslations(array $locales, array $defaultAttributes = []): self
    {
        return $this->addState([
            'translations' => BasicPageTranslationFactory::createMany(\count($locales), array_merge($defaultAttributes, [
                'locale' => self::faker()->unique()->randomElement($locales),
            ])),
        ]);
    }

    /** @return array<string, mixed> */
    protected function getDefaults(): array
    {
        return [
            'location' => self::faker()->randomElement([
                BasicPage::LOCATION_FOOTER,
                BasicPage::LOCATION_NONE,
            ]),
            'publish' => self::faker()->boolean(),
            'metaInformation' => EntityMetaInformationFactory::createOne(),
        ];
    }

    protected static function getClass(): string
    {
        return BasicPage::class;
    }
}
