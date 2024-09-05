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

use Runroom\Testing\Tests\App\Admin\TestAdmin;
use Runroom\Testing\Tests\App\Entity\TestingEntity;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(TestAdmin::class)
        ->public()
        ->tag('sonata.admin', [
            'model_class' => TestingEntity::class,
            'manager_type' => 'orm',
            'label' => 'Test Entity',
        ]);
};
