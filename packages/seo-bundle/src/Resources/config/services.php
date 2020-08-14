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
use Runroom\SeoBundle\Entity\EntityMetaInformation;
use Runroom\SeoBundle\Entity\MetaInformation;
use Runroom\SeoBundle\MetaInformation\DefaultMetaInformationProvider;
use Runroom\SeoBundle\MetaInformation\MetaInformationBuilder;
use Runroom\SeoBundle\MetaInformation\MetaInformationService;
use Runroom\SeoBundle\Repository\MetaInformationRepository;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $containerConfigurator): void {
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
        ->arg('$urlGenerator', ref('router'))
        ->arg('$locales', null);

    $services->set(AlternateLinksService::class)
        ->arg('$requestStack', ref('request_stack'))
        ->arg('$providers', tagged_iterator('runroom.seo.alternate_links'))
        ->arg('$defaultProvider', ref(DefaultAlternateLinksProvider::class))
        ->arg('$builder', ref(AlternateLinksBuilder::class))
        ->tag('kernel.event_subscriber');

    $services->set(DefaultAlternateLinksProvider::class);

    $services->set(MetaInformationBuilder::class)
        ->arg('$repository', ref(MetaInformationRepository::class))
        ->arg('$propertyAccessor', ref('property_accessor'));

    $services->set(MetaInformationService::class)
        ->arg('$requestStack', ref('request_stack'))
        ->arg('$providers', tagged_iterator('runroom.seo.meta_information'))
        ->arg('$defaultProvider', ref(DefaultMetaInformationProvider::class))
        ->arg('$builder', ref(MetaInformationBuilder::class))
        ->tag('kernel.event_subscriber');

    $services->set(DefaultMetaInformationProvider::class);

    $services->set(MetaInformationRepository::class)
        ->arg('$registry', ref('doctrine'))
        ->tag('doctrine.repository_service');
};
