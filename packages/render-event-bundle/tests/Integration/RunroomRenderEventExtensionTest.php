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

namespace Runroom\RenderEventBundle\Tests\Integration;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Runroom\RenderEventBundle\Controller\TemplateController;
use Runroom\RenderEventBundle\DependencyInjection\RunroomRenderEventExtension;
use Runroom\RenderEventBundle\ErrorRenderer\TwigErrorRenderer;
use Runroom\RenderEventBundle\Renderer\PageRenderer;

class RunroomRenderEventExtensionTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->load();
    }

    public function testItHasCoreServicesAlias(): void
    {
        $this->assertContainerBuilderHasService('runroom.render_event.controller.template', TemplateController::class);
        $this->assertContainerBuilderHasService('runroom.render_event.renderer.page', PageRenderer::class);
        $this->assertContainerBuilderHasService('runroom.render_event.error_renderer.twig_error', TwigErrorRenderer::class);
        $this->assertContainerBuilderHasService('runroom.render_event.page_view_model');
    }

    protected function getContainerExtensions(): array
    {
        return [new RunroomRenderEventExtension()];
    }
}
