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

namespace Runroom\CookiesBundle\Admin;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Runroom\CookiesBundle\Entity\CookiesPage;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\Validator\Constraints as Assert;

/** @extends AbstractAdmin<CookiesPage> */
final class CookiesPageAdmin extends AbstractAdmin
{
    /**
     * @todo: Simplify this when dropping support for Sonata 3
     *
     * @param RouteCollection|RouteCollectionInterface $collection
     */
    protected function configureRoutes(object $collection): void
    {
        $collection->remove('create');
        $collection->remove('list');
        $collection->remove('show');
        $collection->remove('delete');
        $collection->remove('batch');
        $collection->remove('export');
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('translations', TranslationsType::class, [
                'label' => false,
                'default_locale' => null,
                'fields' => [
                    'title' => [
                        'label' => 'Title*',
                    ],
                    'content' => [
                        'field_type' => CKEditorType::class,
                    ],
                ],
                'constraints' => [
                    new Assert\Valid(),
                ],
            ]);
    }
}
