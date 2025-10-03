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

final class DefaultAlternateLinksProvider extends AbstractAlternateLinksProvider
{
    #[\Override]
    public function providesAlternateLinks(string $route): bool
    {
        return true;
    }

    public function canGenerateAlternateLink(array $context, string $locale): bool
    {
        return true;
    }

    public function getParameters(array $context, string $locale): ?array
    {
        return null;
    }

    protected function getRoutes(): array
    {
        return [];
    }
}
