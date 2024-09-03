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

namespace Runroom\SeoBundle\MetaInformation;

use Runroom\SeoBundle\ViewModel\MetaInformationViewModel;
use Symfony\Component\HttpFoundation\RequestStack;

final class MetaInformationService implements MetaInformationServiceInterface
{
    /**
     * @param iterable<MetaInformationProviderInterface> $providers
     */
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly iterable $providers,
        private readonly MetaInformationBuilderInterface $builder,
    ) {}

    public function build(array $context): MetaInformationViewModel
    {
        $route = $this->getCurrentRoute();

        return $this->builder->build(
            $this->selectProvider($route),
            $context,
            $route
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

    private function selectProvider(string $route): MetaInformationProviderInterface
    {
        foreach ($this->providers as $provider) {
            if ($provider->providesMetas($route)) {
                return $provider;
            }
        }

        throw new \RuntimeException('There is no provided selected to build meta information.');
    }
}
