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
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(RedirectAdmin::class)
        ->public()
        ->args([null, Redirect::class, null])
        ->tag('sonata.admin', ['manager_type' => 'orm', 'label' => 'Redirects']);

    $services->set(RedirectListener::class)
        ->arg('$repository', ref(RedirectRepository::class))
        ->tag('kernel.event_subscriber');

    $services->set(AutomaticRedirectSubscriber::class)
        ->arg('$urlGenerator', ref('router'))
        ->arg('$propertyAccessor', ref('property_accessor'))
        ->arg('$configuration', []);

    $services->set(RedirectRepository::class)
        ->arg('$registry', ref('doctrine'))
        ->tag('doctrine.repository_service');
};
