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
    /** @return array<string, mixed> */
    protected function getDefaults(): array
    {
        return [
            'translations' => CookiesPageTranslationFactory::createMany(2),
        ];
    }

    protected static function getClass(): string
    {
        return CookiesPage::class;
    }
}
