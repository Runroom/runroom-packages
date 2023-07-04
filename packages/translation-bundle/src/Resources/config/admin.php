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

use Runroom\TranslationBundle\Admin\TranslationAdmin;
use Runroom\TranslationBundle\Entity\Translation;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('runroom.translation.admin.translation', TranslationAdmin::class)
        ->public()
        ->tag('sonata.admin', [
            'model_class' => Translation::class,
            'manager_type' => 'orm',
            'label' => 'Translations',
        ]);
};
