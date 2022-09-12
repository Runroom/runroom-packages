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

namespace Runroom\FormHandlerBundle\Tests\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Runroom\FormHandlerBundle\FormHandler;
use Runroom\FormHandlerBundle\ViewModel\BasicFormViewModel;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class FormHandlerTest extends TestCase
{
    /**
     * @var MockObject&FormFactoryInterface
     */
    private $formFactory;

    private EventDispatcher $eventDispatcher;
    private RequestStack $requestStack;
    private Request $request;
    private Session $session;
    private FormHandler $formHandler;

    protected function setUp(): void
    {
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->eventDispatcher = new EventDispatcher();
        $this->requestStack = new RequestStack();
        $this->session = new Session(new MockArraySessionStorage());
        $this->request = new Request();
        $this->request->setSession($this->session);
        $this->requestStack->push($this->request);

        $this->formHandler = new FormHandler(
            $this->formFactory,
            $this->eventDispatcher,
            $this->requestStack
        );
    }

    /**
     * @test
     */
    public function itThrowsWhenThereIsNoRequest(): void
    {
        $this->requestStack->pop();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('You can not handle a form without a request.');

        $this->formHandler->handleForm(FormType::class);
    }

    /**
     * @test
     */
    public function itHandlesFormsWithoutBeingSubmitted(): void
    {
        $this->configureForm(false);

        $this->eventDispatcher->addListener('form.form_types.event.success', function (): void {
            self::fail("This shouldn't be called");
        });

        $model = $this->formHandler->handleForm(FormType::class);

        static::assertInstanceOf(BasicFormViewModel::class, $model);
    }

    /**
     * @test
     */
    public function itHandlesSubmittedForms(): void
    {
        $form = $this->configureForm();

        $this->eventDispatcher->addListener('form.form_types.event.success', function (GenericEvent $event): void {
            $subject = $event->getSubject();

            self::assertInstanceOf(BasicFormViewModel::class, $subject);
            self::assertTrue($subject->formIsValid());
        });

        $model = $this->formHandler->handleForm(FormType::class);

        static::assertInstanceOf(BasicFormViewModel::class, $model);
        static::assertInstanceOf(FormView::class, $model->getFormView());
        static::assertSame($form, $model->getForm());
        static::assertSame(['success'], $this->session->getFlashBag()->get('form_types'));
    }

    /**
     * @return MockObject&FormInterface
     */
    private function configureForm(bool $submitted = true, bool $valid = true): MockObject
    {
        $form = $this->createMock(FormInterface::class);
        $formView = new FormView();

        $this->formFactory->method('create')->with(FormType::class, null, [])->willReturn($form);

        $form->expects(static::once())->method('handleRequest')->with($this->request);
        $form->method('getName')->willReturn('form_types');
        $form->method('isSubmitted')->willReturn($submitted);
        $form->method('isValid')->willReturn($valid);
        $form->method('createView')->willReturn($formView);

        return $form;
    }
}
