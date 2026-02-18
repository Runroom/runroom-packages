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

namespace Runroom\SeoBundle\Admin;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use Composer\InstalledVersions;
use Runroom\SeoBundle\Entity\EntityMetaInformation;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @extends AbstractAdmin<EntityMetaInformation>
 */
final class EntityMetaInformationAdmin extends AbstractAdmin
{
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->remove('create');
        $collection->remove('edit');
        $collection->remove('list');
        $collection->remove('show');
        $collection->remove('delete');
        $collection->remove('batch');
        $collection->remove('export');
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $newVersion = version_compare((string) InstalledVersions::getVersion('a2lix/translation-form-bundle'), '4.0.0', '>=');

        $form
            ->add('translations', TranslationsType::class, [
                ...($newVersion ? ['translatable_class' => EntityMetaInformation::class] : []),
                'label' => false,
                'default_locale' => null,
                ($newVersion ? 'children' : 'fields') => [
                    'title' => [],
                    'description' => [],
                ],
                'constraints' => [
                    new Assert\Valid(),
                ],
            ]);
    }
}
