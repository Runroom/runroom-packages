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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Runroom\RenderEventBundle\Controller\TemplateController;
use Runroom\RenderEventBundle\Renderer\PageRendererInterface;
use Symfony\Component\HttpFoundation\Response;

final class TemplateControllerTest extends TestCase
{
    private MockObject&PageRendererInterface $renderer;
    private TemplateController $controller;

    protected function setUp(): void
    {
        $this->renderer = $this->createMock(PageRendererInterface::class);

        $this->controller = new TemplateController($this->renderer);
    }

    public function testItRendersTemplate(): void
    {
        $controller = $this->controller;
        $expectedResponse = new Response();

        $this->renderer->method('renderResponse')->with('template.html.twig', ['parameter' => 'value'])
            ->willReturn($expectedResponse);

        $response = $controller('template.html.twig', ['parameter' => 'value']);

        static::assertSame($expectedResponse, $response);
    }
}
