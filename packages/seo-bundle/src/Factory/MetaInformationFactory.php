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

/** @extends ModelFactory<MetaInformation> */
final class MetaInformationFactory extends ModelFactory
{
    /** @return array<string, mixed> */
    protected function getDefaults(): array
    {
        return [
            'route' => self::faker()->unique()->word(),
            'routeName' => self::faker()->words(3, true),
        ];
    }

    /** @param string[] $locales */
    public function withTranslations(array $locales, array $defaultAttributes = []): self
    {
        return $this->addState([
            'translations' => MetaInformationTranslationFactory::createMany(count($locales), \array_merge($defaultAttributes, [
                'locale' => self::faker()->unique()->randomElement($locales),
            ])),
        ]);
    }

    protected static function getClass(): string
    {
        return MetaInformation::class;
    }
}
