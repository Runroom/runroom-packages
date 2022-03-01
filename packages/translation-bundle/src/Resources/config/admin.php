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
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $translationAdmin = $services->set(TranslationAdmin::class)
        ->public()
        ->tag('sonata.admin', [
            'model_class' => Translation::class,
            'manager_type' => 'orm',
            'label' => 'Translations',
        ]);

    /* @todo: Simplify this when dropping support for SonataAdminBundle 3 */
    if (!is_a(CRUDController::class, AbstractController::class, true)) {
        $translationAdmin->args([null, Translation::class, null]);
    }
};
