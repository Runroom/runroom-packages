<?php

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

trait FormAware
{
    protected $form;
    protected $formView;

    public function setForm(FormInterface $form): void
    {
        $this->form = $form;
    }

    public function getForm(): FormInterface
    {
        return $this->form;
    }

    public function getFormView(): ?FormView
    {
        if (null === $this->formView && null !== $this->form) {
            $this->formView = $this->form->createView();
        }

        return $this->formView;
    }

    public function formIsValid(): bool
    {
        return null !== $this->form && $this->form->isSubmitted() && $this->form->isValid();
    }
}
