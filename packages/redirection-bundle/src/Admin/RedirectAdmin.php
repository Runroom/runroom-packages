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

namespace Runroom\RedirectionBundle\Admin;

use Runroom\RedirectionBundle\Entity\Redirect;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * @extends AbstractAdmin<Redirect>
 */
final class RedirectAdmin extends AbstractAdmin
{
    /**
     * @var array<string, int>
     */
    private static array $typeChoices = [
        'redirect.httpCode.permanent' => Redirect::PERMANENT,
        'redirect.httpCode.temporal' => Redirect::TEMPORAL,
    ];

    /**
     * @todo: Simplify this when dropping support for Sonata 3
     *
     * @param RouteCollection|RouteCollectionInterface $collection
     */
    protected function configureRoutes(object $collection): void
    {
        $collection->remove('show');
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('source')
            ->add('destination')
            ->add('httpCode', null, [
                'field_options' => [
                    'choices' => self::$typeChoices,
                ],
                'field_type' => ChoiceType::class,
            ])
            ->add('automatic')
            ->add('publish');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('source', FieldDescriptionInterface::TYPE_URL, [
                'hide_protocol' => true,
                'attributes' => [
                    'target' => '_blank',
                ],
            ])
            ->add('destination', FieldDescriptionInterface::TYPE_URL, [
                'hide_protocol' => true,
                'attributes' => [
                    'target' => '_blank',
                ],
            ])
            ->add('httpCode', FieldDescriptionInterface::TYPE_CHOICE, [
                'choices' => array_flip(self::$typeChoices),
                'editable' => true,
                'catalogue' => 'messages',
            ])
            ->add('publish', null, [
                'editable' => true,
            ])
            ->add('automatic')
            ->add(ListMapper::NAME_ACTIONS, ListMapper::TYPE_ACTIONS, [
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('source', null, [
                'help' => 'redirect.source',
            ])
            ->add('destination', null, [
                'help' => 'redirect.destination',
            ])
            ->add('httpCode', ChoiceType::class, [
                'choices' => self::$typeChoices,
                'label' => false,
                'expanded' => true,
            ])
            ->add('publish');
    }
}
