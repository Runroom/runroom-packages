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
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Runroom\RenderEventBundle\ErrorRenderer\TwigErrorRenderer;
use Runroom\RenderEventBundle\Renderer\PageRenderer;
use Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;
use Twig\Loader\LoaderInterface;

class TwigErrorRendererTest extends TestCase
{
    /** @var Stub&Environment */
    private $twig;

    /** @var MockObject&HtmlErrorRenderer */
    private $fallbackErrorRenderer;

    /** @var MockObject&PageRenderer */
    private $renderer;

    /** @var Stub&\Throwable */
    private $exception;

    /** @var MockObject&FlattenException */
    private $flattenException;

    /** @var MockObject&LoaderInterface */
    private $twigLoader;

    private RequestStack $requestStack;

    protected function setUp(): void
    {
        $this->twig = $this->createStub(Environment::class);
        $this->fallbackErrorRenderer = $this->createMock(HtmlErrorRenderer::class);
        $this->renderer = $this->createMock(PageRenderer::class);
        $this->exception = $this->createStub(\Throwable::class);
        $this->flattenException = $this->createMock(FlattenException::class);
        $this->twigLoader = $this->createMock(LoaderInterface::class);

        $this->requestStack = new RequestStack();
        $this->requestStack->push(new Request());

        $this->twig->method('getLoader')->willReturn($this->twigLoader);
        $this->flattenException->method('getStatusCode')->willReturn(404);
        $this->flattenException->method('getStatusText')->willReturn('status_text');
        $this->flattenException->method('setAsString')->with('renderer_template')->willReturn($this->flattenException);
        $this->fallbackErrorRenderer->method('render')->with($this->exception)
            ->willReturn($this->flattenException);
    }

    /** @test */
    public function itRendersException(): void
    {
        $controller = $this->configureController(true);

        $response = $controller->render($this->exception);

        self::assertSame(404, $response->getStatusCode());
    }

    /** @test */
    public function itRendersGenericErrorPage(): void
    {
        $this->renderer->method('render')->with('@Twig/Exception/error.html.twig', [
            'exception' => $this->flattenException,
            'status_code' => 404,
            'status_text' => 'status_text',
        ])->willReturn('renderer_template');
        $this->twigLoader->method('exists')->willReturnMap([
            ['@Twig/Exception/error404.html.twig', false],
            ['@Twig/Exception/error.html.twig', true],
        ]);

        $controller = $this->configureController();

        $response = $controller->render($this->exception);

        self::assertSame(404, $response->getStatusCode());
    }

    /** @test */
    public function itRenders404ErrorPage(): void
    {
        $this->renderer->method('render')->with('@Twig/Exception/error404.html.twig', [
            'exception' => $this->flattenException,
            'status_code' => 404,
            'status_text' => 'status_text',
        ])->willReturn('renderer_template');
        $this->twigLoader->method('exists')->with('@Twig/Exception/error404.html.twig')->willReturn(true);

        $controller = $this->configureController();

        $response = $controller->render($this->exception);

        self::assertSame(404, $response->getStatusCode());
    }

    /** @test */
    public function itReturnsExceptionIfNoTemplateIsAvailable(): void
    {
        $this->twigLoader->method('exists')->willReturnMap([
            ['@Twig/Exception/error404.html.twig', false],
            ['@Twig/Exception/error.html.twig', false],
        ]);

        $controller = $this->configureController();

        $response = $controller->render($this->exception);

        self::assertSame(404, $response->getStatusCode());
    }

    /** @test */
    public function itReturnsSecondParameterIfRequestStackDoesNotHaveRequest(): void
    {
        self::assertTrue(TwigErrorRenderer::isDebug(new RequestStack(), true)());
        self::assertFalse(TwigErrorRenderer::isDebug(new RequestStack(), false)());
    }

    private function configureController(bool $debug = false): TwigErrorRenderer
    {
        return new TwigErrorRenderer(
            $this->twig,
            $this->fallbackErrorRenderer,
            $this->renderer,
            TwigErrorRenderer::isDebug($this->requestStack, $debug)
        );
    }
}
