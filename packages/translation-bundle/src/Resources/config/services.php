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
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(TranslationAdmin::class)
        ->public()
        ->args([null, Translation::class, null])
        ->tag('sonata.admin', ['manager_type' => 'orm', 'label' => 'Translations']);

    $services->set(TranslationService::class)
        ->arg('$repository', ref(TranslationRepository::class))
        ->arg('$translator', ref('translator'));

    $services->set(TranslationRepository::class)
        ->arg('$registry', ref('doctrine'))
        ->tag('doctrine.repository_service');

    $services->set(TranslationExtension::class)
        ->arg('$service', ref(TranslationService::class))
        ->tag('twig.extension');
};
