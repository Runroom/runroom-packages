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

use PHPUnit\Framework\TestCase;
use Runroom\RedirectionBundle\Listener\RedirectListener;
use Runroom\RedirectionBundle\Repository\RedirectRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelEvents;

final class RedirectListenerTest extends TestCase
{
    private $repository;
    private $listener;

    protected function setUp(): void
    {
        $this->repository = $this->prophesize(RedirectRepository::class);

        $this->listener = new RedirectListener($this->repository->reveal());
    }

    /**
     * @test
     */
    public function itSubscribesToKernelRequestEvent(): void
    {
        $events = $this->listener->getSubscribedEvents();

        $this->assertArrayHasKey(KernelEvents::REQUEST, $events);
    }

    /**
     * @test
     */
    public function itDoesNotDoAnythingIfTheRequestIsNotTheMasterOne(): void
    {
        $event = $this->getResponseEvent(HttpKernelInterface::SUB_REQUEST);

        $this->listener->onKernelRequest($event);

        $this->assertNull($event->getResponse());
    }

    /**
     * @test
     */
    public function itDoesNotDOAnythingIfTheRouteIsNotFoundOnTheRepository(): void
    {
        $this->repository->findRedirect('/')->shouldBeCalled()->willReturn(null);

        $event = $this->getResponseEvent();

        $this->listener->onKernelRequest($event);

        $this->assertNull($event->getResponse());
    }

    /**
     * @test
     */
    public function itDoesARedirectToDestinationUrl(): void
    {
        $this->repository->findRedirect('/')->shouldBeCalled()->willReturn([
            'destination' => '/redirect',
            'httpCode' => 301,
        ]);

        $event = $this->getResponseEvent();

        $this->listener->onKernelRequest($event);

        $response = $event->getResponse();

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame('/redirect', $response->getTargetUrl());
        $this->assertSame(301, $response->getStatusCode());
    }

    private function getResponseEvent(int $requestType = HttpKernelInterface::MASTER_REQUEST): GetResponseEvent
    {
        $kernel = $this->prophesize(Kernel::class);

        return new GetResponseEvent(
            $kernel->reveal(),
            new Request(),
            $requestType
        );
    }
}
