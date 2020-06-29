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

namespace Runroom\RedirectionBundle\Listener;

use Runroom\RedirectionBundle\Repository\RedirectRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class RedirectListener implements EventSubscriberInterface
{
    /** @var RedirectRepository */
    private $repository;

    public function __construct(RedirectRepository $repository)
    {
        $this->repository = $repository;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        if (null !== ($redirect = $this->repository->findRedirect($event->getRequest()->getPathInfo()))) {
            $event->setResponse(
                new RedirectResponse($redirect['destination'], (int) $redirect['httpCode'])
            );
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
