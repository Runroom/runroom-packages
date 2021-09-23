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
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4
    $services = $containerConfigurator->services();

    $services->set('runroom_user.admin.reset_password_request', ResetPasswordRequestAdmin::class)
        ->public()
        ->args([null, ResetPasswordRequest::class, null])
        ->tag('sonata.admin', ['manager_type' => 'orm', 'label' => 'Reset password request']);
};
