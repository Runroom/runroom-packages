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

use Runroom\BasicPageBundle\Admin\BasicPageAdmin;
use Runroom\BasicPageBundle\Entity\BasicPage;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $basicPageAdmin = $services->set('runroom.basic_page.admin.basic_page', BasicPageAdmin::class)
        ->public()
        ->tag('sonata.admin', [
            'model_class' => BasicPage::class,
            'manager_type' => 'orm',
            'label' => 'Basic pages',
        ]);

    /**
     * @todo: Simplify this when dropping support for SonataAdminBundle 3
     */
    if (!is_a(CRUDController::class, AbstractController::class, true)) {
        $basicPageAdmin->args([null, BasicPage::class, null]);
    }
};
