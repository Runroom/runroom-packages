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
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<CookiesPage>
 *
 * @method        CookiesPageFactory addState(array|callable $attributes = [])
 * @method static Proxy<CookiesPage> createOne(array $attributes = [])
 */
final class CookiesPageFactory extends ModelFactory
{
    /**
     * @param string[]             $locales
     * @param array<string, mixed> $defaultAttributes
     */
    public function withTranslations(array $locales, array $defaultAttributes = []): self
    {
        return $this->addState([
            'translations' => CookiesPageTranslationFactory::new(static function () use (&$locales, $defaultAttributes): array {
                return array_merge($defaultAttributes, ['locale' => array_pop($locales)]);
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
        return CookiesPage::class;
    }
}
