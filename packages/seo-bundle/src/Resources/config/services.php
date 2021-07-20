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

use Runroom\SeoBundle\Admin\EntityMetaInformationAdmin;
use Runroom\SeoBundle\Admin\MetaInformationAdmin;
use Runroom\SeoBundle\AlternateLinks\AlternateLinksBuilder;
use Runroom\SeoBundle\AlternateLinks\AlternateLinksService;
use Runroom\SeoBundle\AlternateLinks\DefaultAlternateLinksProvider;
use Runroom\SeoBundle\Context\DefaultContextExtractor;
use Runroom\SeoBundle\Entity\EntityMetaInformation;
use Runroom\SeoBundle\Entity\MetaInformation;
use Runroom\SeoBundle\MetaInformation\DefaultMetaInformationProvider;
use Runroom\SeoBundle\MetaInformation\MetaInformationBuilder;
use Runroom\SeoBundle\MetaInformation\MetaInformationService;
use Runroom\SeoBundle\Repository\MetaInformationRepository;
use Runroom\SeoBundle\Twig\SeoExtension;
use Runroom\SeoBundle\Twig\SeoRuntime;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4.4
    // Use "abstract_arg" function for creating references to arguments without value when dropping support for Symfony 4.4
    $services = $containerConfigurator->services();

    $services->set(MetaInformationAdmin::class)
        ->public()
        ->args([null, MetaInformation::class, null])
        ->tag('sonata.admin', ['manager_type' => 'orm', 'label' => 'SEO']);

    $services->set(EntityMetaInformationAdmin::class)
        ->public()
        ->args([null, EntityMetaInformation::class, null])
        ->tag('sonata.admin', ['manager_type' => 'orm', 'label' => 'Entity SEO']);

    $services->set(AlternateLinksBuilder::class)
        ->arg('$urlGenerator', new ReferenceConfigurator('router'))
        ->arg('$locales', null);

    $services->set(AlternateLinksService::class)
        ->arg('$requestStack', new ReferenceConfigurator('request_stack'))
        ->arg('$providers', tagged_iterator('runroom.seo.alternate_links'))
        ->arg('$builder', new ReferenceConfigurator(AlternateLinksBuilder::class));

    $services->set(DefaultAlternateLinksProvider::class)
        ->tag('runroom.seo.alternate_links', ['priority' => -1]);

    $services->set(DefaultContextExtractor::class)
        ->arg('$modelKey', null);

    $services->set(MetaInformationBuilder::class)
        ->arg('$repository', new ReferenceConfigurator(MetaInformationRepository::class))
        ->arg('$propertyAccessor', new ReferenceConfigurator('property_accessor'));

    $services->set(MetaInformationService::class)
        ->arg('$requestStack', new ReferenceConfigurator('request_stack'))
        ->arg('$providers', tagged_iterator('runroom.seo.meta_information'))
        ->arg('$builder', new ReferenceConfigurator(MetaInformationBuilder::class));

    $services->set(DefaultMetaInformationProvider::class)
        ->tag('runroom.seo.meta_information', ['priority' => -1]);

    $services->set(MetaInformationRepository::class)
        ->arg('$registry', new ReferenceConfigurator('doctrine'))
        ->tag('doctrine.repository_service');

    $services->set(SeoExtension::class)
        ->tag('twig.extension');

    $services->set(SeoRuntime::class)
        ->arg('$alternateLinks', new ReferenceConfigurator(AlternateLinksService::class))
        ->arg('$metaInformation', new ReferenceConfigurator(MetaInformationService::class))
        ->arg('$contextExtractor', null)
        ->tag('twig.runtime');
};
