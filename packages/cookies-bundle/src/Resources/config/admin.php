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

use Runroom\CookiesBundle\Admin\CookiesPageAdmin;
use Runroom\CookiesBundle\Entity\CookiesPage;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('runroom.cookies.admin.cookies_page', CookiesPageAdmin::class)
        ->public()
        ->tag('sonata.admin', [
            'model_class' => CookiesPage::class,
            'manager_type' => 'orm',
            'label' => 'Cookies',
        ]);
};
