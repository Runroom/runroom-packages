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

namespace Runroom\UserBundle\Admin;

use Runroom\UserBundle\Form\RolesMatrixType;
use Runroom\UserBundle\Model\UserInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @extends AbstractAdmin<UserInterface>
 */
final class UserAdmin extends AbstractAdmin
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
    }

    #[\Override]
    public function configureExportFields(): array
    {
        return array_filter(
            parent::configureExportFields(),
            static fn ($field): bool => !\in_array($field, ['password', 'salt'], true)
        );
    }

    /**
     * @param UserInterface $object
     */
    protected function prePersist(object $object): void
    {
        $this->updatePassword($object);

        $object->setCreatedAt(new \DateTime());
    }

    /**
     * @param UserInterface $object
     */
    protected function preUpdate(object $object): void
    {
        $this->updatePassword($object);
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->remove('show');
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('email')
            ->add('enabled');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('createdAt')
            ->add('email')
            ->add('enabled', null, [
                'editable' => true,
            ])
            ->add(ListMapper::NAME_ACTIONS, ListMapper::TYPE_ACTIONS, [
                'translation_domain' => 'messages',
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                    'impersonate' => [
                        'template' => '@RunroomUser/impersonate_user.html.twig',
                    ],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('General', [
                'class' => 'col-md-4',
                'box_class' => 'box box-solid box-primary',
            ])
                ->add('email')
                ->add('plainPassword', TextType::class, [
                    'required' => $this->isCurrentRoute('create'),
                ])
                ->add('enabled')
            ->end()
            ->with('Roles', [
                'class' => 'col-md-8',
                'box_class' => 'box box-solid box-primary',
            ])
                ->add('roles', RolesMatrixType::class, [
                    'label' => false,
                    'required' => false,
                    'expanded' => true,
                    'multiple' => true,
                ])
            ->end();
    }

    private function updatePassword(UserInterface $user): void
    {
        $plainPassword = $user->getPlainPassword();

        if (null === $plainPassword) {
            return;
        }

        $password = $this->passwordHasher->hashPassword($user, $plainPassword);

        $user->setPlainPassword(null);
        $user->setPassword($password);
    }
}
