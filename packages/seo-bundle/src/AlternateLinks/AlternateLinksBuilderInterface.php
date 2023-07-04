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

interface AlternateLinksBuilderInterface
{
    /**
     * @param array<string, mixed>  $context
     * @param array<string, string> $routeParameters
     *
     * @return array<string, string>
     */
    public function build(
        AlternateLinksProviderInterface $provider,
        array $context,
        string $route,
        array $routeParameters = []
    ): array;
}
