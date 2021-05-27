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

use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/** @final */
class AlternateLinksBuilder
{
    private UrlGeneratorInterface $urlGenerator;

    /** @var string[] */
    private array $locales;

    /** @param string[] $locales */
    public function __construct(UrlGeneratorInterface $urlGenerator, array $locales)
    {
        $this->urlGenerator = $urlGenerator;
        $this->locales = $locales;
    }

    /**
     * @param mixed $model
     * @param array<string, string> $routeParameters
     *
     * @return array<string, string>
     */
    public function build(
        AlternateLinksProviderInterface $provider,
        $model,
        string $route,
        array $routeParameters = []
    ): array {
        $alternateLinks = [];

        foreach ($this->getAvailableLocales($provider, $model) as $locale) {
            try {
                $alternateLinks[$locale] = $this->urlGenerator->generate(
                    $route . '.' . $locale,
                    $provider->getParameters($model, $locale) ?? $routeParameters,
                    UrlGeneratorInterface::ABSOLUTE_URL
                );
            } catch (RouteNotFoundException $e) {
            }
        }

        return $alternateLinks;
    }

    /**
     * @param mixed $model
     *
     * @return string[]
     */
    protected function getAvailableLocales(AlternateLinksProviderInterface $provider, $model): array
    {
        return array_intersect(
            $this->locales,
            $provider->getAvailableLocales($model) ?? $this->locales
        );
    }
}
