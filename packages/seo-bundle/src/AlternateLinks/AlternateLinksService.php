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

final readonly class AlternateLinksService implements AlternateLinksServiceInterface
{
    private const EXCLUDED_PARAMETERS = ['_locale', '_fragment'];

    /**
     * @param iterable<AlternateLinksProviderInterface> $providers
     */
    public function __construct(
        private RequestStack $requestStack,
        private iterable $providers,
        private AlternateLinksBuilderInterface $builder,
    ) {}

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

        if (null === $request) {
            return '';
        }

        $route = $request->attributes->get('_route', '');
        \assert(\is_string($route));

        return $route;
    }

    /**
     * @return array<string, string>
     */
    private function getCurrentRouteParameters(): array
    {
        $request = $this->requestStack->getCurrentRequest();

        /**
         * @var array<string, string>
         */
        $routeParameters = null !== $request ? $request->attributes->get('_route_params', []) : [];

        return array_diff_key($routeParameters, array_flip(self::EXCLUDED_PARAMETERS));
    }

    private function selectProvider(string $route): AlternateLinksProviderInterface
    {
        foreach ($this->providers as $provider) {
            if ($provider->providesAlternateLinks($route)) {
                return $provider;
            }
        }

        throw new \RuntimeException('There is no provided selected to build alternate links.');
    }
}
