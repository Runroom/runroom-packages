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

use Runroom\UserBundle\Admin\UserAdmin;
use Runroom\UserBundle\Entity\User;
use Runroom\UserBundle\Twig\GlobalVariables;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;
use Symfony\Component\Security\Http\Authentication\AuthenticatorManager;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4
    $services = $containerConfigurator->services();

    /**
     * @todo: Simplify this when dropping support for Symfony 4
     */
    $passwordHasherId = class_exists(AuthenticatorManager::class) ? 'security.password_hasher' : 'security.password_encoder';

    $userAdmin = $services->set('runroom.user.admin.user', UserAdmin::class)
        ->public()
        ->tag('sonata.admin', [
            'model_class' => User::class,
            'manager_type' => 'orm',
            'label' => 'User',
        ]);

    /**
     * @todo: Simplify this when dropping support for SonataAdminBundle 3
     */
    if (!is_a(CRUDController::class, AbstractController::class, true)) {
        $userAdmin->args([null, User::class, null, new ReferenceConfigurator($passwordHasherId)]);
    } else {
        $userAdmin->arg('$passwordHasher', new ReferenceConfigurator($passwordHasherId));
    }

    $services->set('runroom.user.twig.global_variables', GlobalVariables::class)
        ->arg('$pool', new ReferenceConfigurator('sonata.admin.pool'))
        ->arg('$hasRequestPasswordEnabled', null);
};
