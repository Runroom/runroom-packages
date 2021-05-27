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
use Runroom\BasicPageBundle\Controller\BasicPageController;
use Runroom\BasicPageBundle\Service\BasicPageService;
use Runroom\BasicPageBundle\ViewModel\BasicPageViewModel;
use Runroom\RenderEventBundle\Renderer\PageRenderer;
use Symfony\Component\HttpFoundation\Response;

class BasicPageControllerTest extends TestCase
{
    /** @var MockObject&PageRenderer */
    private $renderer;

    /** @var MockObject&BasicPageService */
    private $service;

    private BasicPageController $controller;

    protected function setUp(): void
    {
        $this->renderer = $this->createMock(PageRenderer::class);
        $this->service = $this->createMock(BasicPageService::class);

        $this->controller = new BasicPageController(
            $this->renderer,
            $this->service
        );
    }

    /** @test */
    public function itRendersStatic(): void
    {
        $model = new BasicPageViewModel();
        $expectedResponse = new Response();

        $this->service->method('getBasicPageViewModel')->with('slug')->willReturn($model);
        $this->renderer->method('renderResponse')->with('@RunroomBasicPage/show.html.twig', $model, null)
            ->willReturn($expectedResponse);

        $response = $this->controller->show('slug');

        self::assertSame($expectedResponse, $response);
    }
}
