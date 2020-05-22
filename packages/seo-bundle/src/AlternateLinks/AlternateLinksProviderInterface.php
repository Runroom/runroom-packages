<?php

/*
 * This file is part of the Runroom package.
 *
 * (c) Runroom <runroom@runroom.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Runroom\SeoBundle\AlternateLinks;

interface AlternateLinksProviderInterface
{
    public function providesAlternateLinks(string $route): bool;

    public function getAvailableLocales($model): ?array;

    public function getParameters($model, string $locale): ?array;
}
