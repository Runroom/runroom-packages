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

namespace Runroom\SeoBundle\AlternateLinks;

abstract class AbstractAlternateLinksProvider implements AlternateLinksProviderInterface
{
    public function providesAlternateLinks(string $route): bool
    {
        return \in_array($route, $this->getRoutes(), true);
    }

    public function getAvailableLocales($model): ?array
    {
        return null;
    }

    abstract public function getParameters($model, string $locale): ?array;

    abstract protected function getRoutes(): array;
}
