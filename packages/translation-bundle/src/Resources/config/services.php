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

use Runroom\TranslationBundle\Repository\TranslationRepository;
use Runroom\TranslationBundle\Service\TranslationService;
use Runroom\TranslationBundle\Twig\TranslationExtension;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('runroom.translation.service.translation', TranslationService::class)
        ->arg('$repository', service(TranslationRepository::class))
        ->arg('$translator', service('translator'));

    $services->set(TranslationRepository::class)
        ->arg('$registry', service('doctrine'))
        ->tag('doctrine.repository_service');

    $services->set('runroom.translation.twig.translation', TranslationExtension::class)
        ->arg('$service', service('runroom.translation.service.translation'))
        ->tag('twig.extension');
};
