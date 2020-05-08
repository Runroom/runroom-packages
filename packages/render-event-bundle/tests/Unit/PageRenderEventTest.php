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

namespace Runroom\RenderEventBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Runroom\RenderEventBundle\Event\PageRenderEvent;
use Runroom\RenderEventBundle\ViewModel\PageViewModel;
use Symfony\Component\HttpFoundation\Response;

class PageRenderEventTest extends TestCase
{
    use ProphecyTrait;

    protected $pageViewModel;
    protected $response;
    protected $pageRenderEvent;

    protected function setUp(): void
    {
        $this->pageViewModel = new PageViewModel();
        $this->response = new Response();

        $this->pageRenderEvent = new PageRenderEvent(
            'view',
            $this->pageViewModel,
            $this->response
        );
    }

    /**
     * @test
     */
    public function itSetsPageViewModel()
    {
        $expectedViewModel = new PageViewModel();

        $this->pageRenderEvent->setPageViewModel($expectedViewModel);

        $viewModel = $this->pageRenderEvent->getPageViewModel();

        $this->assertSame($expectedViewModel, $viewModel);
    }

    /**
     * @test
     */
    public function itGetsPageViewModel()
    {
        $this->pageViewModel->setContent('model');

        $viewModel = $this->pageRenderEvent->getPageViewModel();

        $this->assertInstanceOf(PageViewModel::class, $viewModel);
        $this->assertSame('model', $viewModel->getContent());
    }
}
