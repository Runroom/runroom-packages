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
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(BasicPageAdmin::class)
        ->public()
        ->args([null, BasicPage::class, null])
        ->tag('sonata.admin', ['manager_type' => 'orm', 'label' => 'Basic pages']);
};
