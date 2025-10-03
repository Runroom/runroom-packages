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
use Runroom\FormHandlerBundle\EventSubscriber\FormRenderSubscriber;
use Runroom\FormHandlerBundle\ViewModel\BasicFormViewModel;
use Runroom\RenderEventBundle\Event\PageRenderEvent;
use Runroom\RenderEventBundle\ViewModel\PageViewModel;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

final class FormRenderSubscriberTest extends TestCase
{
    public function testItDoesSubscribeToOnFlushEvent(): void
    {
        static::assertArrayHasKey(PageRenderEvent::EVENT_NAME, FormRenderSubscriber::getSubscribedEvents());
    }

    public function testItSets422OnResponseIfFormIsInvalidSubmitted(): void
    {
        $form = static::createStub(FormInterface::class);
        $form->method('isSubmitted')->willReturn(true);
        $form->method('isValid')->willReturn(false);

        $basicFormModel = new BasicFormViewModel();
        $basicFormModel->setForm($form);

        $pageModel = new PageViewModel();
        $pageModel->setContent($basicFormModel);

        $event = new PageRenderEvent('view.html.twig', $pageModel);

        $subscriber = new FormRenderSubscriber();
        $subscriber->pageRenderEvent($event);

        static::assertInstanceOf(Response::class, $event->getResponse());
        static::assertSame(422, $event->getResponse()->getStatusCode());
    }
}
