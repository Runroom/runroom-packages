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

use Runroom\FormHandlerBundle\EventSubscriber\FormRenderSubscriber;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('runroom.form_handler.event_subscriber.form_render', FormRenderSubscriber::class)
        ->tag('kernel.event_subscriber');
};
