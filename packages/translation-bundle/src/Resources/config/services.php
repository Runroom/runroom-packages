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

use Runroom\TranslationBundle\Admin\TranslationAdmin;
use Runroom\TranslationBundle\Entity\Translation;
use Runroom\TranslationBundle\Repository\TranslationRepository;
use Runroom\TranslationBundle\Service\TranslationService;
use Runroom\TranslationBundle\Twig\TranslationExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4.4
    $services = $containerConfigurator->services();

    $services->set(TranslationAdmin::class)
        ->public()
        ->args([null, Translation::class, null])
        ->tag('sonata.admin', ['manager_type' => 'orm', 'label' => 'Translations']);

    $services->set(TranslationService::class)
        ->arg('$repository', new ReferenceConfigurator(TranslationRepository::class))
        ->arg('$translator', new ReferenceConfigurator('translator'));

    $services->set(TranslationRepository::class)
        ->arg('$registry', new ReferenceConfigurator('doctrine'))
        ->tag('doctrine.repository_service');

    $services->set(TranslationExtension::class)
        ->arg('$service', new ReferenceConfigurator(TranslationService::class))
        ->tag('twig.extension');
};
