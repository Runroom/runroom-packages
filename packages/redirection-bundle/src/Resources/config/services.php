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

use Runroom\RedirectionBundle\EventSubscriber\AutomaticRedirectSubscriber;
use Runroom\RedirectionBundle\EventSubscriber\RedirectSubscriber;
use Runroom\RedirectionBundle\Repository\RedirectRepository;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4
    $services = $containerConfigurator->services();

    $services->set('runroom.redirection.event_subscriber.redirect', RedirectSubscriber::class)
        ->arg('$repository', new ReferenceConfigurator(RedirectRepository::class))
        ->tag('kernel.event_subscriber');

    $services->set('runroom.redirection.event_subscriber.automatic_redirect', AutomaticRedirectSubscriber::class)
        ->arg('$urlGenerator', new ReferenceConfigurator('router'))
        ->arg('$propertyAccessor', new ReferenceConfigurator('property_accessor'))
        ->arg('$configuration', []);

    $services->set(RedirectRepository::class)
        ->arg('$registry', new ReferenceConfigurator('doctrine'))
        ->tag('doctrine.repository_service');
};
