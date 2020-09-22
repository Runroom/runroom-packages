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
use Symfony\Component\Validator\Constraints as Assert;

/** @extends AbstractAdmin<CookiesPage> */
final class CookiesPageAdmin extends AbstractAdmin
{
    protected function configureRoutes(RouteCollection $collection): void
    {
        $collection->remove('show');
        $collection->remove('delete');
        $collection->remove('list');
        $collection->remove('batch');
        $collection->remove('export');
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('Translations', [
                'box_class' => 'box box-solid box-primary',
            ])
                ->add('translations', TranslationsType::class, [
                    'label' => false,
                    'required' => false,
                    'fields' => [
                        'title' => [],
                        'content' => [
                            'field_type' => CKEditorType::class,
                        ],
                    ],
                    'constraints' => [
                        new Assert\Valid(),
                    ],
                ])
            ->end();
    }
}
