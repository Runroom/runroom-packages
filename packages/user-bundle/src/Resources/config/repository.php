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

use Runroom\UserBundle\Entity\User;
use Runroom\UserBundle\Repository\UserRepository;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('runroom.user.repository.user', UserRepository::class)
        ->arg('$entityManager', service('doctrine.orm.entity_manager'))
        ->arg('$class', User::class);
};
