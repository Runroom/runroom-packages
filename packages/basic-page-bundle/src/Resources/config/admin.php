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

use Runroom\BasicPageBundle\Admin\BasicPageAdmin;
use Runroom\BasicPageBundle\Entity\BasicPage;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('runroom.basic_page.admin.basic_page', BasicPageAdmin::class)
        ->public()
        ->tag('sonata.admin', [
            'model_class' => BasicPage::class,
            'manager_type' => 'orm',
            'label' => 'Basic pages',
        ]);
};
