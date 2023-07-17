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

use Runroom\SeoBundle\AlternateLinks\AlternateLinksBuilder;
use Runroom\SeoBundle\AlternateLinks\AlternateLinksService;
use Runroom\SeoBundle\AlternateLinks\DefaultAlternateLinksProvider;
use Runroom\SeoBundle\MetaInformation\DefaultMetaInformationProvider;
use Runroom\SeoBundle\MetaInformation\MetaInformationBuilder;
use Runroom\SeoBundle\MetaInformation\MetaInformationService;
use Runroom\SeoBundle\Repository\MetaInformationRepository;
use Runroom\SeoBundle\Twig\SeoExtension;
use Runroom\SeoBundle\Twig\SeoRuntime;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "abstract_arg" function for creating references to arguments without value when dropping support for Symfony 4
    $services = $containerConfigurator->services();

    $services->set('runroom.seo.alternate_links.builder', AlternateLinksBuilder::class)
        ->arg('$urlGenerator', service('router'))
        ->arg('$locales', null);

    $services->set('runroom.seo.alternate_links.service', AlternateLinksService::class)
        ->arg('$requestStack', service('request_stack'))
        ->arg('$providers', tagged_iterator('runroom.seo.alternate_links'))
        ->arg('$builder', service('runroom.seo.alternate_links.builder'));

    $services->set('runroom.seo.alternate_links.default_provider', DefaultAlternateLinksProvider::class)
        ->tag('runroom.seo.alternate_links', ['priority' => -1]);

    $services->set('runroom.seo.meta_information.builder', MetaInformationBuilder::class)
        ->arg('$repository', service(MetaInformationRepository::class))
        ->arg('$propertyAccessor', service('property_accessor'));

    $services->set('runroom.seo.meta_information.service', MetaInformationService::class)
        ->arg('$requestStack', service('request_stack'))
        ->arg('$providers', tagged_iterator('runroom.seo.meta_information'))
        ->arg('$builder', service('runroom.seo.meta_information.builder'));

    $services->set('runroom.seo.meta_information.default_provider', DefaultMetaInformationProvider::class)
        ->tag('runroom.seo.meta_information', ['priority' => -1]);

    $services->set(MetaInformationRepository::class)
        ->arg('$registry', service('doctrine'))
        ->tag('doctrine.repository_service');

    $services->set('runroom.seo.twig.seo', SeoExtension::class)
        ->tag('twig.extension');

    $services->set('runroom.seo.twig.seo_runtime', SeoRuntime::class)
        ->arg('$alternateLinks', service('runroom.seo.alternate_links.service'))
        ->arg('$metaInformation', service('runroom.seo.meta_information.service'))
        ->tag('twig.runtime');
};
