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
use Runroom\SeoBundle\Entity\MetaInformation;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @extends AbstractAdmin<MetaInformation>
 */
final class MetaInformationAdmin extends AbstractAdmin
{
    /**
     * @param mixed[] $sortValues
     */
    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues['_sort_by'] = 'routeName';
    }

    /**
     * @todo: Simplify this when dropping support for Sonata 3
     *
     * @param RouteCollection|RouteCollectionInterface $collection
     */
    protected function configureRoutes(object $collection): void
    {
        $collection->remove('create');
        $collection->remove('delete');
        $collection->remove('show');
        $collection->remove('batch');
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('routeName');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('routeName')
            ->add('title', null, [
                'sortable' => true,
                'sort_field_mapping' => [
                    'fieldName' => 'title',
                ],
                'sort_parent_association_mappings' => [[
                    'fieldName' => 'translations',
                ]],
            ])
            ->add('description', null, [
                'sortable' => true,
                'sort_field_mapping' => [
                    'fieldName' => 'description',
                ],
                'sort_parent_association_mappings' => [[
                    'fieldName' => 'translations',
                ]],
            ])
            ->add(ListMapper::NAME_ACTIONS, ListMapper::TYPE_ACTIONS, [
                'actions' => [
                    'edit' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('Translations', [
                'box_class' => 'box box-solid box-primary',
            ])
                ->add('translations', TranslationsType::class, [
                    'label' => false,
                    'default_locale' => null,
                    'fields' => [
                        'title' => [
                            'label' => 'Title*',
                        ],
                        'description' => [
                            'label' => 'Description*',
                        ],
                    ],
                    'constraints' => [
                        new Assert\Valid(),
                    ],
                ])
            ->end()
            ->with('Image', [
                'box_class' => 'box box-solid box-primary',
            ])
                ->add('image', ModelListType::class, [
                    'required' => false,
                ], [
                    'link_parameters' => [
                        'context' => 'default',
                        'provider' => 'sonata.media.provider.image',
                    ],
                ])
            ->end();
    }
}
