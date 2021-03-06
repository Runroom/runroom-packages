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

namespace Runroom\RedirectionBundle\Tests\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Runroom\RedirectionBundle\Listener\RedirectListener;
use Runroom\RedirectionBundle\Repository\RedirectRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelEvents;

class RedirectListenerTest extends TestCase
{
    /** @var MockObject&RedirectRepository */
    private $repository;

    private RedirectListener $listener;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(RedirectRepository::class);

        $this->listener = new RedirectListener($this->repository);
    }

    /** @test */
    public function itSubscribesToKernelRequestEvent(): void
    {
        $events = RedirectListener::getSubscribedEvents();

        self::assertArrayHasKey(KernelEvents::REQUEST, $events);
    }

    /** @test */
    public function itDoesNotDoAnythingIfTheRequestIsNotTheMasterOne(): void
    {
        $event = $this->getResponseEvent(HttpKernelInterface::SUB_REQUEST);

        $this->listener->onKernelRequest($event);

        self::assertNull($event->getResponse());
    }

    /** @test */
    public function itDoesNotDOAnythingIfTheRouteIsNotFoundOnTheRepository(): void
    {
        $this->repository->expects(self::once())->method('findRedirect')->with('/');

        $event = $this->getResponseEvent();

        $this->listener->onKernelRequest($event);

        self::assertNull($event->getResponse());
    }

    /** @test */
    public function itDoesARedirectToDestinationUrl(): void
    {
        $this->repository->expects(self::once())->method('findRedirect')->with('/')->willReturn([
            'destination' => '/redirect',
            'httpCode' => 301,
        ]);

        $event = $this->getResponseEvent();

        $this->listener->onKernelRequest($event);

        /** @var RedirectResponse */
        $response = $event->getResponse();

        self::assertSame('/redirect', $response->getTargetUrl());
        self::assertSame(301, $response->getStatusCode());
    }

    private function getResponseEvent(int $requestType = HttpKernelInterface::MASTER_REQUEST): RequestEvent
    {
        return new RequestEvent($this->createStub(Kernel::class), new Request(), $requestType);
    }
}
