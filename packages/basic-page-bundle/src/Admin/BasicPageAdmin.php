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

namespace Runroom\BasicPageBundle\Admin;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Runroom\BasicPageBundle\Entity\BasicPage;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\AdminType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @extends AbstractAdmin<BasicPage>
 */
final class BasicPageAdmin extends AbstractAdmin
{
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('translations.title', null, [
                'label' => 'Title',
            ])
            ->add('publish');
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->remove('show');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('title', null, [
                'sortable' => true,
                'sort_field_mapping' => [
                    'fieldName' => 'title',
                ],
                'sort_parent_association_mappings' => [[
                    'fieldName' => 'translations',
                ]],
            ])
            ->add('location', FieldDescriptionInterface::TYPE_CHOICE, [
                'editable' => true,
                'choices' => [
                    BasicPage::LOCATION_NONE => 'None',
                    BasicPage::LOCATION_FOOTER => 'Footer',
                ],
            ])
            ->add('publish', FieldDescriptionInterface::TYPE_BOOLEAN, [
                'editable' => true,
            ])
            ->add(ListMapper::NAME_ACTIONS, ListMapper::TYPE_ACTIONS, [
                'actions' => [
                    'open' => [
                        'template' => '@RunroomBasicPage/open.html.twig',
                    ],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('Basic', [
                'box_class' => 'box box-solid box-primary',
            ])
                ->add('translations', TranslationsType::class, [
                    'label' => false,
                    'default_locale' => null,
                    'fields' => [
                        'title' => [
                            'label' => 'Title*',
                        ],
                        'content' => [
                            'label' => 'Content*',
                            'field_type' => CKEditorType::class,
                        ],
                        'slug' => [
                            'field_type' => HiddenType::class,
                        ],
                    ],
                    'constraints' => [
                        new Assert\Valid(),
                    ],
                ])
            ->end()
            ->with('Published', [
                'box_class' => 'box box-solid box-primary',
            ])
                ->add('publish')
                ->add('location', ChoiceType::class, [
                    'choices' => [
                        'None' => BasicPage::LOCATION_NONE,
                        'Footer' => BasicPage::LOCATION_FOOTER,
                    ],
                ])
            ->end()
            ->with('SEO', [
                'box_class' => 'box box-solid box-primary',
            ])
                ->add('metaInformation', AdminType::class, [], [
                    'edit' => 'inline',
                ])
            ->end();
    }
}
