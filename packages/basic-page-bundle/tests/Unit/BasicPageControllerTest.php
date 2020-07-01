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
use Runroom\BasicPageBundle\Controller\BasicPageController;
use Runroom\BasicPageBundle\Service\BasicPageService;
use Runroom\BasicPageBundle\ViewModel\BasicPageViewModel;
use Runroom\RenderEventBundle\Renderer\PageRenderer;
use Symfony\Component\HttpFoundation\Response;

class BasicPageControllerTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<PageRenderer> */
    private $renderer;

    /** @var ObjectProphecy<BasicPageService> */
    private $service;

    /** @var BasicPageController */
    private $controller;

    protected function setUp(): void
    {
        $this->renderer = $this->prophesize(PageRenderer::class);
        $this->service = $this->prophesize(BasicPageService::class);

        $this->controller = new BasicPageController(
            $this->renderer->reveal(),
            $this->service->reveal()
        );
    }

    /** @test */
    public function itRendersStatic(): void
    {
        $model = new BasicPageViewModel();
        $expectedResponse = $this->prophesize(Response::class);

        $this->service->getBasicPageViewModel('slug')->willReturn($model);
        $this->renderer->renderResponse('@RunroomBasicPage/show.html.twig', $model, null)
            ->willReturn($expectedResponse->reveal());

        $response = $this->controller->show('slug');

        self::assertSame($expectedResponse->reveal(), $response);
    }
}
