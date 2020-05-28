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

namespace Runroom\FormHandlerBundle;

use Runroom\FormHandlerBundle\ViewModel\BasicFormViewModel;
use Runroom\FormHandlerBundle\ViewModel\FormAwareInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

class FormHandler
{
    /** @var FormFactoryInterface */
    private $formFactory;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var RequestStack */
    private $requestStack;

    /** @var Session */
    private $session;

    public function __construct(
        FormFactoryInterface $formFactory,
        EventDispatcherInterface $eventDispatcher,
        RequestStack $requestStack,
        Session $session
    ) {
        $this->formFactory = $formFactory;
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack = $requestStack;
        $this->session = $session;
    }

    public function handleForm(string $type, array $options = [], FormAwareInterface $model = null): FormAwareInterface
    {
        $form = $this->formFactory->create($type, null, $options);
        $form->handleRequest($this->requestStack->getCurrentRequest());

        $model = $model ?? new BasicFormViewModel();
        $model->setForm($form);

        if ($model->formIsValid()) {
            $this->eventDispatcher->dispatch(
                new GenericEvent($model),
                'form.' . $form->getName() . '.event.success'
            );

            $this->session->getFlashBag()->add($form->getName(), 'success');
        }

        return $model;
    }
}
