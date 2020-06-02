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

namespace Runroom\RenderEventBundle\ErrorRenderer;

use Runroom\RenderEventBundle\Renderer\PageRenderer;
use Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface;
use Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

final class TwigErrorRenderer implements ErrorRendererInterface
{
    /** @var Environment */
    private $twig;

    /** @var HtmlErrorRenderer */
    private $fallbackErrorRenderer;

    /** @var PageRenderer */
    private $renderer;

    /** @var bool|callable */
    private $debug;

    /** @param bool|callable $debug */
    public function __construct(
        Environment $twig,
        HtmlErrorRenderer $fallbackErrorRenderer = null,
        PageRenderer $renderer,
        $debug = false
    ) {
        $this->twig = $twig;
        $this->fallbackErrorRenderer = $fallbackErrorRenderer ?? new HtmlErrorRenderer();
        $this->renderer = $renderer;
        $this->debug = $debug;
    }

    public function render(\Throwable $exception): FlattenException
    {
        $exception = $this->fallbackErrorRenderer->render($exception);
        $debug = \is_bool($this->debug) ? $this->debug : ($this->debug)($exception);

        if ($debug || !$template = $this->findTemplate($exception->getStatusCode())) {
            return $exception;
        }

        return $exception->setAsString($this->renderer->render($template, [
            'exception' => $exception,
            'status_code' => $exception->getStatusCode(),
            'status_text' => $exception->getStatusText(),
        ]));
    }

    public static function isDebug(RequestStack $requestStack, bool $debug): \Closure
    {
        return static function () use ($requestStack, $debug): bool {
            if (!$request = $requestStack->getCurrentRequest()) {
                return $debug;
            }

            return $debug && $request->attributes->getBoolean('showException', true);
        };
    }

    private function findTemplate(int $statusCode): ?string
    {
        $template = sprintf('@Twig/Exception/error%s.html.twig', $statusCode);
        if ($this->twig->getLoader()->exists($template)) {
            return $template;
        }

        $template = '@Twig/Exception/error.html.twig';
        if ($this->twig->getLoader()->exists($template)) {
            return $template;
        }

        return null;
    }
}
