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
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class FormHandler
{
    private FormFactoryInterface $formFactory;
    private EventDispatcherInterface $eventDispatcher;
    private RequestStack $requestStack;

    public function __construct(
        FormFactoryInterface $formFactory,
        EventDispatcherInterface $eventDispatcher,
        RequestStack $requestStack
    ) {
        $this->formFactory = $formFactory;
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack = $requestStack;
    }

    /**
     * @param class-string<FormTypeInterface> $type
     * @param array<string, mixed> $options
     */
    public function handleForm(string $type, array $options = [], FormAwareInterface $model = null): FormAwareInterface
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            throw new \RuntimeException('You can not handle a form without a request.');
        }

        $form = $this->formFactory->create($type, null, $options);
        $form->handleRequest($request);

        $model ??= new BasicFormViewModel();
        $model->setForm($form);

        if ($model->formIsValid()) {
            $this->eventDispatcher->dispatch(
                new GenericEvent($model),
                'form.' . $form->getName() . '.event.success'
            );

            $request->getSession()->getFlashBag()->add($form->getName(), 'success');
        }

        return $model;
    }
}
