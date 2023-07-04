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

namespace Runroom\FormHandlerBundle\EventSubscriber;

use Runroom\FormHandlerBundle\ViewModel\FormAwareInterface;
use Runroom\RenderEventBundle\Event\PageRenderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;

final class FormRenderSubscriber implements EventSubscriberInterface
{
    public function pageRenderEvent(PageRenderEvent $event): void
    {
        $content = $event->getPageViewModel()->getContent();
        $response = $event->getResponse() ?? new Response();

        if ($content instanceof FormAwareInterface && 200 === $response->getStatusCode()) {
            $form = $content->getForm();

            if (null !== $form && $form->isSubmitted() && !$form->isValid()) {
                $response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $event->setResponse($response);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [PageRenderEvent::EVENT_NAME => 'pageRenderEvent'];
    }
}
