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
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Validator\Constraints as Assert;

final class TranslationAdmin extends AbstractAdmin
{
    protected $datagridValues = [
        '_page' => 1,
        '_sort_order' => 'ASC',
        '_sort_by' => 'key',
    ];

    protected function configureRoutes(RouteCollection $collection): void
    {
        $collection->remove('create');
        $collection->remove('delete');
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('key')
            ->add('translations.value', null, ['label' => 'Value']);
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('key')
            ->add('value', 'html', [
                'sortable' => true,
                'sort_field_mapping' => ['fieldName' => 'value'],
                'sort_parent_association_mappings' => [['fieldName' => 'translations']],
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
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
