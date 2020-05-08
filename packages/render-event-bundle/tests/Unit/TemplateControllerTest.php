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
use Runroom\RenderEventBundle\Controller\TemplateController;
use Runroom\RenderEventBundle\Renderer\PageRenderer;
use Symfony\Component\HttpFoundation\Response;

class TemplateControllerTest extends TestCase
{
    use ProphecyTrait;

    protected $renderer;
    protected $controller;

    protected function setUp(): void
    {
        $this->renderer = $this->prophesize(PageRenderer::class);

        $this->controller = new TemplateController($this->renderer->reveal());
    }

    /**
     * @test
     */
    public function itRendersTemplate()
    {
        $controller = $this->controller;
        $expectedResponse = new Response();

        $this->renderer->renderResponse('template.html.twig', ['parameter' => 'value'])
            ->willReturn($expectedResponse);

        $response = $controller('template.html.twig', ['parameter' => 'value']);

        $this->assertSame($expectedResponse, $response);
    }
}
