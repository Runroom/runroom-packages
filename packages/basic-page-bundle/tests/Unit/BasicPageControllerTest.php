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
use Runroom\BasicPageBundle\Controller\BasicPageController;
use Runroom\BasicPageBundle\Service\BasicPageService;
use Runroom\BasicPageBundle\ViewModel\BasicPageViewModel;
use Runroom\RenderEventBundle\Renderer\PageRenderer;
use Symfony\Component\HttpFoundation\Response;

class BasicPageControllerTest extends TestCase
{
    use ProphecyTrait;

    protected const STATICS = '@RunroomBasicPage/show.html.twig';
    protected const SLUG = 'slug';

    protected $renderer;
    protected $service;
    protected $controller;

    protected function setUp(): void
    {
        $this->renderer = $this->prophesize(PageRenderer::class);
        $this->service = $this->prophesize(BasicPageService::class);

        $this->controller = new BasicPageController(
            $this->renderer->reveal(),
            $this->service->reveal()
        );
    }

    /**
     * @test
     */
    public function itRendersStatic()
    {
        $model = new BasicPageViewModel();
        $expectedResponse = $this->prophesize(Response::class);

        $this->service->getBasicPageViewModel(self::SLUG)->willReturn($model);
        $this->renderer->renderResponse(self::STATICS, $model, null)
            ->willReturn($expectedResponse->reveal());

        $response = $this->controller->show(self::SLUG);

        $this->assertSame($expectedResponse->reveal(), $response);
    }
}
