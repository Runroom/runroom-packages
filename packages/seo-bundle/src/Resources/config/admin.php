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
use Runroom\SeoBundle\Entity\EntityMetaInformation;
use Runroom\SeoBundle\Entity\MetaInformation;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

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
};
