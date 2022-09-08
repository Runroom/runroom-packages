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

use Runroom\UserBundle\Admin\ResetPasswordRequestAdmin;
use Runroom\UserBundle\Entity\ResetPasswordRequest;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $resetPasswordRequestAdmin = $services->set('runroom.user.admin.reset_password_request', ResetPasswordRequestAdmin::class)
        ->public()
        ->tag('sonata.admin', [
            'model_class' => ResetPasswordRequest::class,
            'manager_type' => 'orm',
            'label' => 'Reset password request',
        ]);

    /**
     * @todo: Simplify this when dropping support for SonataAdminBundle 3
     */
    if (!is_a(CRUDController::class, AbstractController::class, true)) {
        $resetPasswordRequestAdmin->args([null, ResetPasswordRequest::class, null]);
    }
};
