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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Runroom\RedirectionBundle\EventListener\AutomaticRedirectListener;
use Runroom\RedirectionBundle\EventSubscriber\RedirectSubscriber;
use Runroom\RedirectionBundle\Repository\RedirectRepository;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('runroom.redirection.event_subscriber.redirect', RedirectSubscriber::class)
        ->arg('$repository', service(RedirectRepository::class))
        ->tag('kernel.event_subscriber');

    $services->set('runroom.redirection.event_listener.automatic_redirect', AutomaticRedirectListener::class)
        ->arg('$urlGenerator', service('router'))
        ->arg('$propertyAccessor', service('property_accessor'))
        ->arg('$configuration', [])
        ->tag('doctrine.event_listener', ['event' => 'onFlush']);

    $services->set(RedirectRepository::class)
        ->arg('$registry', service('doctrine'))
        ->tag('doctrine.repository_service');
};
