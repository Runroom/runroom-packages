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

use Runroom\RenderEventBundle\Event\PageRenderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class MetaInformationService implements EventSubscriberInterface
{
    private RequestStack $requestStack;

    /** @var iterable<MetaInformationProviderInterface> */
    private iterable $providers;

    private DefaultMetaInformationProvider $defaultProvider;
    private MetaInformationBuilder $builder;

    /** @param iterable<MetaInformationProviderInterface> $providers */
    public function __construct(
        RequestStack $requestStack,
        iterable $providers,
        DefaultMetaInformationProvider $defaultProvider,
        MetaInformationBuilder $builder
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

        $metas = $this->builder->build(
            $this->selectProvider($route),
            $route,
            $page->getContent()
        );

        $page->addContext('metas', $metas);

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

    private function selectProvider(string $route): MetaInformationProviderInterface
    {
        foreach ($this->providers as $provider) {
            if ($provider->providesMetas($route)) {
                return $provider;
            }
        }

        return $this->defaultProvider;
    }
}
