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

use Symfony\Component\HttpFoundation\RequestStack;

final class AlternateLinksService implements AlternateLinksServiceInterface
{
    private const EXCLUDED_PARAMETERS = ['_locale', '_fragment'];

    private RequestStack $requestStack;

    /** @var iterable<AlternateLinksProviderInterface> */
    private iterable $providers;

    private AlternateLinksBuilder $builder;

    /** @param iterable<AlternateLinksProviderInterface> $providers */
    public function __construct(
        RequestStack $requestStack,
        iterable $providers,
        AlternateLinksBuilder $builder
    ) {
        $this->requestStack = $requestStack;
        $this->providers = $providers;
        $this->builder = $builder;
    }

    public function build(array $context): array
    {
        $route = $this->getCurrentRoute();

        return $this->builder->build(
            $this->selectProvider($route),
            $context,
            $route,
            $this->getCurrentRouteParameters()
        );
    }

    private function getCurrentRoute(): string
    {
        $request = $this->requestStack->getCurrentRequest();

        return null !== $request ? $request->get('_route', '') : '';
    }

    /** @return array<string, string> */
    private function getCurrentRouteParameters(): array
    {
        $request = $this->requestStack->getCurrentRequest();

        $routeParameters = null !== $request ? $request->get('_route_params', []) : [];

        return array_diff_key($routeParameters, array_flip(self::EXCLUDED_PARAMETERS));
    }

    private function selectProvider(string $route): AlternateLinksProviderInterface
    {
        foreach ($this->providers as $provider) {
            if ($provider->providesAlternateLinks($route)) {
                return $provider;
            }
        }

        throw new \RuntimeException('There is no provided selected to build alternate links');
    }
}
