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
use Prophecy\Prophecy\ObjectProphecy;
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
    use ProphecyTrait;

    /** @var ObjectProphecy<Environment> */
    private $twig;

    /** @var ObjectProphecy<HtmlErrorRenderer> */
    private $fallbackErrorRenderer;

    /** @var ObjectProphecy<PageRenderer> */
    private $renderer;

    /** @var ObjectProphecy<\Throwable> */
    private $exception;

    /** @var ObjectProphecy<FlattenException> */
    private $flattenException;

    /** @var ObjectProphecy<LoaderInterface> */
    private $twigLoader;

    /** @var RequestStack */
    private $requestStack;

    protected function setUp(): void
    {
        $this->twig = $this->prophesize(Environment::class);
        $this->fallbackErrorRenderer = $this->prophesize(HtmlErrorRenderer::class);
        $this->renderer = $this->prophesize(PageRenderer::class);
        $this->exception = $this->prophesize(\Throwable::class);
        $this->flattenException = $this->prophesize(FlattenException::class);
        $this->twigLoader = $this->prophesize(LoaderInterface::class);

        $this->requestStack = new RequestStack();
        $this->requestStack->push(new Request());

        $this->twig->getLoader()->willReturn($this->twigLoader->reveal());
        $this->flattenException->getStatusCode()->willReturn(404);
        $this->flattenException->getStatusText()->willReturn('status_text');
        $this->flattenException->setAsString('renderer_template')->willReturn($this->flattenException->reveal());
        $this->fallbackErrorRenderer->render($this->exception->reveal())
            ->willReturn($this->flattenException->reveal());
    }

    /** @test */
    public function itRendersException(): void
    {
        $controller = $this->configureController(true);

        $response = $controller->render($this->exception->reveal());

        self::assertSame(404, $response->getStatusCode());
    }

    /** @test */
    public function itRendersGenericErrorPage(): void
    {
        $this->renderer->render('@Twig/Exception/error.html.twig', [
            'exception' => $this->flattenException->reveal(),
            'status_code' => 404,
            'status_text' => 'status_text',
        ])->willReturn('renderer_template');
        $this->twigLoader->exists('@Twig/Exception/error404.html.twig')->willReturn(false);
        $this->twigLoader->exists('@Twig/Exception/error.html.twig')->willReturn(true);

        $controller = $this->configureController();

        $response = $controller->render($this->exception->reveal());

        self::assertSame(404, $response->getStatusCode());
    }

    /** @test */
    public function itRenders404ErrorPage(): void
    {
        $this->renderer->render('@Twig/Exception/error404.html.twig', [
            'exception' => $this->flattenException->reveal(),
            'status_code' => 404,
            'status_text' => 'status_text',
        ])->willReturn('renderer_template');
        $this->twigLoader->exists('@Twig/Exception/error404.html.twig')->willReturn(true);

        $controller = $this->configureController();

        $response = $controller->render($this->exception->reveal());

        self::assertSame(404, $response->getStatusCode());
    }

    private function configureController(bool $debug = false): TwigErrorRenderer
    {
        return new TwigErrorRenderer(
            $this->twig->reveal(),
            $this->fallbackErrorRenderer->reveal(),
            $this->renderer->reveal(),
            TwigErrorRenderer::isDebug($this->requestStack, $debug)
        );
    }
}
