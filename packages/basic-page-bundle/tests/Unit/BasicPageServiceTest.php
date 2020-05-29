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

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Runroom\BasicPageBundle\Entity\BasicPage;
use Runroom\BasicPageBundle\Repository\BasicPageRepository;
use Runroom\BasicPageBundle\Service\BasicPageService;
use Runroom\BasicPageBundle\ViewModel\BasicPageViewModel;
use Runroom\RenderEventBundle\Event\PageRenderEvent;
use Runroom\RenderEventBundle\ViewModel\PageViewModel;

class BasicPageServiceTest extends TestCase
{
    use ProphecyTrait;

    private const SLUG = 'slug';

    /** @var ObjectProphecy<BasicPageRepository> */
    private $repository;

    /** @var BasicPageService */
    private $service;

    protected function setUp(): void
    {
        $this->repository = $this->prophesize(BasicPageRepository::class);

        $this->service = new BasicPageService($this->repository->reveal());
    }

    /** @test */
    public function itGetsBasicViewModel(): void
    {
        $basicPage = new BasicPage();

        $this->repository->findBySlug(self::SLUG)->willReturn($basicPage);

        $model = $this->service->getBasicPageViewModel(self::SLUG);

        $this->assertInstanceOf(BasicPageViewModel::class, $model);
        $this->assertSame($basicPage, $model->getBasicPage());
    }

    /** @test */
    public function itAddsBasicPagesToPageViewModel(): void
    {
        $event = new PageRenderEvent('view', new PageViewModel());

        $this->repository->findBy(['publish' => true])->willReturn([]);

        $this->service->onPageRender($event);

        $this->assertSame([], $event->getPageViewModel()->getContext('basic_pages'));
    }

    /** @test */
    public function itHasSubscribedEvents(): void
    {
        $events = $this->service->getSubscribedEvents();

        $this->assertCount(1, $events);
    }
}
