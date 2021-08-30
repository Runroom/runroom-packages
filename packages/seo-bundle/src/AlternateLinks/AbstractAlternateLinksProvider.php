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

    abstract public function canGenerateAlternateLink(array $context, string $locale): bool;

    abstract public function getParameters(array $context, string $locale): ?array;

    /** @return string[] */
    abstract protected function getRoutes(): array;
}
