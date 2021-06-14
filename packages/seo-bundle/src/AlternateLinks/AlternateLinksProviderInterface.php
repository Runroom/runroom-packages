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

use Runroom\SeoBundle\Model\SeoModelInterface;

/** @phpstan-template T of SeoModelInterface */
interface AlternateLinksProviderInterface
{
    public function providesAlternateLinks(string $route): bool;

    /** @phpstan-param T $model */
    public function canGenerateAlternateLink(SeoModelInterface $model, string $locale): bool;

    /**
     * @phpstan-param T $model
     *
     * @return array<string, string|null>|null
     */
    public function getParameters(SeoModelInterface $model, string $locale): ?array;
}
