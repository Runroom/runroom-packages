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

use Runroom\FormHandlerBundle\FormHandler;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4
    $services = $containerConfigurator->services();

    $services->set('runroom.form_handler.form_handler', FormHandler::class)
        ->arg('$formFactory', new ReferenceConfigurator('form.factory'))
        ->arg('$eventDispatcher', new ReferenceConfigurator('event_dispatcher'))
        ->arg('$requestStack', new ReferenceConfigurator('request_stack'));
};
