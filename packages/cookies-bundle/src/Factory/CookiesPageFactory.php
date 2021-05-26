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

namespace Runroom\CookiesBundle\Factory;

use Runroom\CookiesBundle\Entity\CookiesPage;
use Zenstruck\Foundry\ModelFactory;

/** @extends ModelFactory<CookiesPage> */
final class CookiesPageFactory extends ModelFactory
{
    /**
     * @param string[] $locales
     * @param array<string, mixed> $defaultAttributes
     */
    public function withTranslations(array $locales, array $defaultAttributes = []): self
    {
        return $this->addState([
            'translations' => CookiesPageTranslationFactory::createMany(\count($locales), array_merge($defaultAttributes, [
                'locale' => self::faker()->unique()->randomElement($locales),
            ])),
        ]);
    }

    /** @return array<string, mixed> */
    protected function getDefaults(): array
    {
        return [];
    }

    protected static function getClass(): string
    {
        return CookiesPage::class;
    }
}
