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

use Runroom\RenderEventBundle\Event\PageRenderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class AlternateLinksService implements EventSubscriberInterface
{
    private const EXCLUDED_PARAMETERS = ['_locale', '_fragment'];

    private RequestStack $requestStack;

    /** @var iterable<AlternateLinksProviderInterface> */
    private iterable $providers;

    private DefaultAlternateLinksProvider $defaultProvider;
    private AlternateLinksBuilder $builder;

    /** @param iterable<AlternateLinksProviderInterface> $providers */
    public function __construct(
        RequestStack $requestStack,
        iterable $providers,
        DefaultAlternateLinksProvider $defaultProvider,
        AlternateLinksBuilder $builder
    ) {
        $this->requestStack = $requestStack;
        $this->providers = $providers;
        $this->defaultProvider = $defaultProvider;
        $this->builder = $builder;
    }

    public function onPageRender(PageRenderEvent $event): void
    {
        $page = $event->getPageViewModel();
        $route = $this->getCurrentRoute();

        $alternateLinks = $this->builder->build(
            $this->selectProvider($route),
            $page->getContent(),
            $route,
            $this->getCurrentRouteParameters()
        );

        $page->addContext('alternate_links', $alternateLinks);

        $event->setPageViewModel($page);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PageRenderEvent::EVENT_NAME => 'onPageRender',
        ];
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

        return $this->defaultProvider;
    }
}
