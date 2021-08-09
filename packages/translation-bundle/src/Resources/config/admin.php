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

use Runroom\TranslationBundle\Admin\TranslationAdmin;
use Runroom\TranslationBundle\Entity\Translation;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(TranslationAdmin::class)
        ->public()
        ->args([null, Translation::class, null])
        ->tag('sonata.admin', ['manager_type' => 'orm', 'label' => 'Translations']);
};
