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

namespace Runroom\RedirectionBundle\EventSubscriber;

use Runroom\RedirectionBundle\Repository\RedirectRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class RedirectSubscriber implements EventSubscriberInterface
{
    private RedirectRepository $repository;

    public function __construct(RedirectRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @todo: Simplify when dropping support for Symfony 4
     *
     * @psalm-suppress UndefinedMethod
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        /**
         * @phpstan-ignore-next-line
         */
        $isMainRequest = method_exists($event, 'isMainRequest') ? $event->isMainRequest() : $event->isMasterRequest();

        if (!$isMainRequest) {
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
