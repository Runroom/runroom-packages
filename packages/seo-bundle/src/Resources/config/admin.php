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
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $metaInformationAdmin = $services->set('runroom.seo.admin.meta_information', MetaInformationAdmin::class)
        ->public()
        ->tag('sonata.admin', [
            'model_class' => MetaInformation::class,
            'manager_type' => 'orm',
            'label' => 'SEO',
        ]);

    /**
     * @todo: Simplify this when dropping support for SonataAdminBundle 3
     */
    if (!is_a(CRUDController::class, AbstractController::class, true)) {
        $metaInformationAdmin->args([null, MetaInformation::class, null]);
    }

    $entityMetaInformationAdmin = $services->set('runroom.seo.admin.entity_meta_information', EntityMetaInformationAdmin::class)
        ->public()
        ->tag('sonata.admin', [
            'model_class' => EntityMetaInformation::class,
            'manager_type' => 'orm',
            'label' => 'Entity SEO',
        ]);

    /**
     * @todo: Simplify this when dropping support for SonataAdminBundle 3
     */
    if (!is_a(CRUDController::class, AbstractController::class, true)) {
        $entityMetaInformationAdmin->args([null, EntityMetaInformation::class, null]);
    }
};
