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

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
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
    use ProphecyTrait;

    /** @var ObjectProphecy<FormFactoryInterface> */
    private $formFactory;

    /** @var EventDispatcher */
    private $eventDispatcher;

    /** @var RequestStack */
    private $requestStack;

    /** @var Request */
    private $request;

    /** @var Session */
    private $session;

    /** @var FormHandler */
    private $formHandler;

    protected function setUp(): void
    {
        $this->formFactory = $this->prophesize(FormFactoryInterface::class);
        $this->eventDispatcher = new EventDispatcher();
        $this->requestStack = new RequestStack();
        $this->request = new Request();
        $this->requestStack->push($this->request);
        $this->session = new Session(new MockArraySessionStorage());

        $this->formHandler = new FormHandler(
            $this->formFactory->reveal(),
            $this->eventDispatcher,
            $this->requestStack,
            $this->session
        );
    }

    /** @test */
    public function itHandlesFormsWithoutBeingSubmitted(): void
    {
        $this->configureForm(false);

        $this->eventDispatcher->addListener('form.form_types.event.success', function (): void {
            self::fail("This shouldn't be called");
        });

        $model = $this->formHandler->handleForm(FormType::class);

        self::assertInstanceOf(BasicFormViewModel::class, $model);
    }

    /** @test */
    public function itHandlesSubmittedForms(): void
    {
        $form = $this->configureForm();

        $this->eventDispatcher->addListener('form.form_types.event.success', function (GenericEvent $event): void {
            self::assertTrue($event->getSubject()->formIsValid());
        });

        $model = $this->formHandler->handleForm(FormType::class);

        self::assertInstanceOf(BasicFormViewModel::class, $model);
        self::assertInstanceOf(FormView::class, $model->getFormView());
        self::assertSame($form->reveal(), $model->getForm());
        self::assertSame(['success'], $this->session->getFlashBag()->get('form_types'));
    }

    /** @return ObjectProphecy<FormInterface> */
    private function configureForm(bool $submitted = true, bool $valid = true): ObjectProphecy
    {
        $form = $this->prophesize(FormInterface::class);
        $formView = new FormView();

        $this->formFactory->create(FormType::class, null, [])->willReturn($form->reveal());

        $form->handleRequest($this->request)->shouldBeCalled();
        $form->getName()->willReturn('form_types');
        $form->isSubmitted()->willReturn($submitted);
        $form->isValid()->willReturn($valid);
        $form->createView()->willReturn($formView);

        return $form;
    }
}
