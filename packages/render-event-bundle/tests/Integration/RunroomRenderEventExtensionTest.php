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
use Runroom\RenderEventBundle\Renderer\PageRenderer;

class RunroomRenderEventExtensionTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->load();
    }

    /** @test */
    public function itHasCoreServicesAlias(): void
    {
        $this->assertContainerBuilderHasService(PageRenderer::class);
        $this->assertContainerBuilderHasService(TemplateController::class);
    }

    protected function getContainerExtensions(): array
    {
        return [new RunroomRenderEventExtension()];
    }
}
