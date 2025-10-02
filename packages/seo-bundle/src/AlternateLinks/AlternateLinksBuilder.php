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

use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final readonly class AlternateLinksBuilder implements AlternateLinksBuilderInterface
{
    /**
     * @param string[] $locales
     */
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private array $locales,
    ) {}

    public function build(
        AlternateLinksProviderInterface $provider,
        array $context,
        string $route,
        array $routeParameters = [],
    ): array {
        $alternateLinks = [];

        foreach ($this->locales as $locale) {
            try {
                if ($provider->canGenerateAlternateLink($context, $locale)) {
                    $alternateLinks[$locale] = $this->urlGenerator->generate(
                        $route . '.' . $locale,
                        $provider->getParameters($context, $locale) ?? $routeParameters,
                        UrlGeneratorInterface::ABSOLUTE_URL
                    );
                }
            } catch (RouteNotFoundException|InvalidParameterException) {
            }
        }

        return $alternateLinks;
    }
}
