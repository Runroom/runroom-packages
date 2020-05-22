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

class DefaultAlternateLinksProvider extends AbstractAlternateLinksProvider
{
    public function providesAlternateLinks(string $route): bool
    {
        return true;
    }

    public function getParameters($model, string $locale): ?array
    {
        return null;
    }

    protected function getRoutes(): array
    {
        return [];
    }
}
