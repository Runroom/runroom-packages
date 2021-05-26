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

namespace Runroom\TranslationBundle\Admin;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Runroom\TranslationBundle\Entity\Translation;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Validator\Constraints as Assert;

/** @extends AbstractAdmin<Translation> */
final class TranslationAdmin extends AbstractAdmin
{
    /** @param mixed[] $sortValues */
    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues['_sort_by'] = 'key';
    }

    protected function configureRoutes(RouteCollection $collection): void
    {
        $collection->remove('create');
        $collection->remove('delete');
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('key')
            ->add('translations.value', null, ['label' => 'Value']);
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('key')
            ->add('value', 'html', [
                'sortable' => true,
                'sort_field_mapping' => ['fieldName' => 'value'],
                'sort_parent_association_mappings' => [['fieldName' => 'translations']],
            ]);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('key')
            ->add('translations', TranslationsType::class, [
                'label' => false,
                'required' => false,
                'fields' => [
                    'value' => [
                        'field_type' => CKEditorType::class,
                        'config' => [
                            'entities' => false,
                            'enterMode' => 'CKEDITOR.ENTER_BR',
                            'toolbar' => [
                                ['Bold', 'Italic'],
                                ['RemoveFormat'],
                                ['Link', 'Unlink'],
                            ],
                        ],
                    ],
                ],
                'constraints' => [
                    new Assert\Valid(),
                ],
            ]);
    }
}
