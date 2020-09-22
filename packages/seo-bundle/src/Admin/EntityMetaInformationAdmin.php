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
use Runroom\SeoBundle\Entity\EntityMetaInformation;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Validator\Constraints as Assert;

/** @extends AbstractAdmin<EntityMetaInformation> */
final class EntityMetaInformationAdmin extends AbstractAdmin
{
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
                        'description' => [],
                    ],
                    'constraints' => [
                        new Assert\Valid(),
                    ],
                ])
            ->end();
    }
}
