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

namespace Runroom\Testing\Tests\App\Admin;

use Runroom\Testing\Tests\App\Entity\TestingEntity;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * @extends AbstractAdmin<TestingEntity>
 */
final class TestAdmin extends AbstractAdmin
{
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->remove('delete');
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter->add('id');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list->add('id');
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('id');
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form->add('id');
    }
}
