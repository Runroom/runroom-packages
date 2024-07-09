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

final class FormHandler implements FormHandlerInterface
{
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly RequestStack $requestStack
    ) {}

    public function handleForm(string $type, array $options = [], ?FormAwareInterface $model = null): FormAwareInterface
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

            /**
             * @todo: Use instanceof FlashBagAwareSessionInterface when dropping Symfony 5 support.
             *
             * @phpstan-ignore-next-line
             * @psalm-suppress UndefinedInterfaceMethod
             */
            $request->getSession()->getFlashBag()->add($form->getName(), 'success');
        }

        return $model;
    }
}
