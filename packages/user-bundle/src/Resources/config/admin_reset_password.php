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

use Runroom\UserBundle\Admin\ResetPasswordRequestAdmin;
use Runroom\UserBundle\Entity\ResetPasswordRequest;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('runroom.user.admin.reset_password_request', ResetPasswordRequestAdmin::class)
        ->public()
        ->tag('sonata.admin', [
            'model_class' => ResetPasswordRequest::class,
            'manager_type' => 'orm',
            'label' => 'Reset password request',
        ]);
};
