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

namespace Runroom\CookiesBundle\Tests\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Runroom\CookiesBundle\Controller\CookiesPageController;
use Runroom\CookiesBundle\Service\CookiesPageService;
use Runroom\CookiesBundle\ViewModel\CookiesPageViewModel;
use Runroom\RenderEventBundle\Renderer\PageRenderer;
use Symfony\Component\HttpFoundation\Response;

class CookiesPageControllerTest extends TestCase
{
    /** @var MockObject&PageRenderer */
    private $renderer;

    /** @var MockObject&CookiesPageService */
    private $service;

    private CookiesPageController $controller;

    protected function setUp(): void
    {
        $this->renderer = $this->createMock(PageRenderer::class);
        $this->service = $this->createMock(CookiesPageService::class);

        $this->controller = new CookiesPageController(
            $this->renderer,
            $this->service
        );
    }

    /** @test */
    public function itRendersCookiesPage(): void
    {
        $viewModel = new CookiesPageViewModel();

        $this->service->expects(self::once())->method('getViewModel')->willReturn($viewModel);
        $this->renderer->method('renderResponse')->with('@RunroomCookies/show.html.twig', $viewModel)
            ->willReturn(new Response());

        $response = $this->controller->index();

        self::assertSame(200, $response->getStatusCode());
    }
}
