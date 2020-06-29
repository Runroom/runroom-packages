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

namespace Tests\Runroom\CookiesBundle\Unit;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Runroom\CookiesBundle\Controller\CookiesPageController;
use Runroom\CookiesBundle\Service\CookiesPageService;
use Runroom\CookiesBundle\ViewModel\CookiesPageViewModel;
use Runroom\RenderEventBundle\Renderer\PageRenderer;
use Symfony\Component\HttpFoundation\Response;

class CookiesPageControllerTest extends TestCase
{
    use ProphecyTrait;

    protected const VIEW = 'pages/cookies.html.twig';

    /** @var ObjectProphecy<PageRenderer> */
    protected $renderer;

    /** @var ObjectProphecy<CookiesPageService> */
    protected $service;

    /** @var CookiesPageController */
    protected $controller;

    protected function setUp(): void
    {
        $this->renderer = $this->prophesize(PageRenderer::class);
        $this->service = $this->prophesize(CookiesPageService::class);

        $this->controller = new CookiesPageController(
            $this->renderer->reveal(),
            $this->service->reveal()
        );
    }

    /** @test */
    public function itRendersCookiesPage(): void
    {
        $viewModel = $this->prophesize(CookiesPageViewModel::class);

        $this->service->getViewModel()->shouldBeCalled()->willReturn($viewModel->reveal());
        $this->renderer->renderResponse(self::VIEW, $viewModel->reveal())->willReturn(new Response());

        $response = $this->controller->index();

        self::assertSame(200, $response->getStatusCode());
    }
}
