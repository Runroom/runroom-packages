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

namespace Runroom\FormHandlerBundle\ViewModel;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

interface FormAwareInterface
{
    /**
     * @param FormInterface<object|null> $form
     */
    public function setForm(FormInterface $form): void;

    /**
     * @return FormInterface<object|null>|null
     */
    public function getForm(): ?FormInterface;

    public function getFormView(): ?FormView;

    public function formIsValid(): bool;
}
