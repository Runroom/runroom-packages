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

namespace Runroom\BasicPageBundle\Tests\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Runroom\BasicPageBundle\Entity\BasicPage;
use Runroom\BasicPageBundle\Repository\BasicPageRepository;
use Runroom\BasicPageBundle\Service\BasicPageService;
use Runroom\RenderEventBundle\Event\PageRenderEvent;
use Runroom\RenderEventBundle\ViewModel\PageViewModel;

class BasicPageServiceTest extends TestCase
{
    /** @var MockObject&BasicPageRepository */
    private $repository;

    private BasicPageService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(BasicPageRepository::class);

        $this->service = new BasicPageService($this->repository);
    }

    /** @test */
    public function itGetsBasicViewModel(): void
    {
        $basicPage = new BasicPage();

        $this->repository->method('findBySlug')->with('slug')->willReturn($basicPage);

        $model = $this->service->getBasicPageViewModel('slug');

        self::assertSame($basicPage, $model->getBasicPage());
    }

    /** @test */
    public function itAddsBasicPagesToPageViewModel(): void
    {
        $event = new PageRenderEvent('view', new PageViewModel());

        $this->repository->method('findBy')->with(['publish' => true])->willReturn([]);

        $this->service->onPageRender($event);

        self::assertSame([], $event->getPageViewModel()->getContext('basic_pages'));
    }

    /** @test */
    public function itHasSubscribedEvents(): void
    {
        $events = BasicPageService::getSubscribedEvents();

        self::assertCount(1, $events);
    }
}
