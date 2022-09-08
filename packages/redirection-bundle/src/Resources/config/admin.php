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

use Runroom\RedirectionBundle\Admin\RedirectAdmin;
use Runroom\RedirectionBundle\Entity\Redirect;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $redirectAdmin = $services->set('runroom.redirection.admin.redirect', RedirectAdmin::class)
        ->public()
        ->tag('sonata.admin', [
            'model_class' => Redirect::class,
            'manager_type' => 'orm',
            'label' => 'Redirects',
        ]);

    /**
     * @todo: Simplify this when dropping support for SonataAdminBundle 3
     */
    if (!is_a(CRUDController::class, AbstractController::class, true)) {
        $redirectAdmin->args([null, Redirect::class, null]);
    }
};
