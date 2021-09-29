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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/** @extends AbstractAdmin<UserInterface> */
final class UserAdmin extends AbstractAdmin
{
    /**
     * @todo: Simplify this when dropping support for Symfony 4
     *
     * @var UserPasswordHasherInterface|UserPasswordEncoderInterface|null
     */
    private ?object $passwordHasher = null;

    /**
     * @todo: Simplify this when dropping support for Symfony 4
     *
     * @param UserPasswordHasherInterface|UserPasswordEncoderInterface $passwordHasher
     */
    public function setPasswordHasher(object $passwordHasher): void
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function configureExportFields(): array
    {
        return array_filter(parent::configureExportFields(), static function ($field): bool {
            return !\in_array($field, ['password', 'salt'], true);
        });
    }

    /**
     * @todo: Add typehint when dropping support for Sonata 3
     *
     * @param UserInterface $object
     */
    public function prePersist($object): void
    {
        $this->updatePassword($object);

        $object->setCreatedAt(new \DateTimeImmutable());
    }

    /**
     * @todo: Add typehint when dropping support for Sonata 3
     *
     * @param UserInterface $object
     */
    public function preUpdate($object): void
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
            ->addIdentifier('email')
            ->add('enabled', null, [
                'editable' => true,
            ])
            ->add('_action', 'actions', [
                'translation_domain' => 'messages',
                'actions' => [
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
                    'mapped' => false,
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
        if (null === $this->passwordHasher) {
            return;
        }

        /** @var mixed[] */
        $submittedData = $this->getRequest()->request->get($this->getUniqId());

        if (!isset($submittedData['plainPassword']) || '' === $submittedData['plainPassword']) {
            return;
        }

        /* @todo: Simplify this when dropping support for Symfony 4 */
        if ($this->passwordHasher instanceof UserPasswordHasherInterface) {
            $password = $this->passwordHasher->hashPassword($user, $submittedData['plainPassword']);
        } else {
            $password = $this->passwordHasher->encodePassword($user, $submittedData['plainPassword']);
        }

        $user->setPassword($password);
    }
}
