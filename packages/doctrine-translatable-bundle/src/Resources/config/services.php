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

use Runroom\DoctrineTranslatableBundle\EventSubscriber\TranslatableEventSubscriber;
use Runroom\DoctrineTranslatableBundle\Provider\LocaleProvider;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('runroom_doctrine_translatable_fetch_mode', 'LAZY');
    $parameters->set('runroom_doctrine_translation_fetch_mode', 'LAZY');

    $services = $containerConfigurator->services();

    $services->set('runroom.doctrine_translatable.event_subscriber.translatable', TranslatableEventSubscriber::class)
        ->public()
        ->arg('$localeProvider', service('runroom.doctrine_translatable.provider.locale'))
        ->arg('$translatableFetchMode', param('runroom_doctrine_translatable_fetch_mode'))
        ->arg('$translationFetchMode', param('runroom_doctrine_translation_fetch_mode'))
        ->tag('doctrine.event_listener', ['event' => 'loadClassMetadata', 'priority' => 10])
        ->tag('doctrine.event_listener', ['event' => 'postLoad', 'priority' => 10])
        ->tag('doctrine.event_listener', ['event' => 'prePersist', 'priority' => 10]);

    $services->set('runroom.doctrine_translatable.provider.locale', LocaleProvider::class)
        ->public()
        ->arg('$requestStack', service('request_stack'))
        ->arg('$parameterBag', service('parameter_bag'))
        ->arg('$translator', service('translator'));
};
