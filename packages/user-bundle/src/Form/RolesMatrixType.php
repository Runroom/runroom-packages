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

namespace Runroom\UserBundle\Form;

use Runroom\UserBundle\Security\RolesBuilder\MatrixRolesBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RolesMatrixType extends AbstractType
{
    private MatrixRolesBuilderInterface $rolesBuilder;

    public function __construct(MatrixRolesBuilderInterface $rolesBuilder)
    {
        $this->rolesBuilder = $rolesBuilder;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'expanded' => true,
            'choices' => function (Options $options, array $parentChoices): array {
                if ([] !== $parentChoices) {
                    return [];
                }

                $roles = $this->rolesBuilder->getRoles($options['choice_translation_domain']);
                $roles = array_keys($roles);

                return array_combine($roles, $roles);
            },
            'choice_translation_domain' => static function (Options $options, $value): ?string {
                if (true === $value) {
                    $value = $options['translation_domain'];
                }
                if (null === $value) {
                    $admin = null;

                    if (isset($options['sonata_admin'])) {
                        $admin = $options['sonata_admin'];
                    }

                    if (null === $admin && isset($options['sonata_field_description'])) {
                        $admin = $options['sonata_field_description']->getAdmin();
                    }

                    if (null !== $admin) {
                        $value = $admin->getTranslationDomain();
                    }
                }

                return $value;
            },
            'data_class' => null,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'sonata_roles_matrix';
    }
}
