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

use Runroom\RedirectionBundle\Admin\RedirectAdmin;
use Runroom\RedirectionBundle\Entity\Redirect;
use Runroom\RedirectionBundle\Listener\AutomaticRedirectSubscriber;
use Runroom\RedirectionBundle\Listener\RedirectListener;
use Runroom\RedirectionBundle\Repository\RedirectRepository;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4.4
    $services = $containerConfigurator->services();

    $services->set(RedirectAdmin::class)
        ->public()
        ->args([null, Redirect::class, null])
        ->tag('sonata.admin', ['manager_type' => 'orm', 'label' => 'Redirects']);

    $services->set(RedirectListener::class)
        ->arg('$repository', new ReferenceConfigurator(RedirectRepository::class))
        ->tag('kernel.event_subscriber');

    $services->set(AutomaticRedirectSubscriber::class)
        ->arg('$urlGenerator', new ReferenceConfigurator('router'))
        ->arg('$propertyAccessor', new ReferenceConfigurator('property_accessor'))
        ->arg('$configuration', []);

    $services->set(RedirectRepository::class)
        ->arg('$registry', new ReferenceConfigurator('doctrine'))
        ->tag('doctrine.repository_service');
};
