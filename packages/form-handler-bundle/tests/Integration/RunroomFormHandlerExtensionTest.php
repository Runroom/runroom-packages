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

namespace Runroom\FormHandlerBundle\Tests\Integration;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Runroom\FormHandlerBundle\DependencyInjection\RunroomFormHandlerExtension;
use Runroom\FormHandlerBundle\EventSubscriber\FormRenderSubscriber;
use Runroom\FormHandlerBundle\FormHandler;

class RunroomFormHandlerExtensionTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->container->setParameter('kernel.bundles', ['RunroomRenderEventBundle' => true]);

        $this->load();
    }

    /**
     * @test
     */
    public function itHasCoreServicesAlias(): void
    {
        $this->assertContainerBuilderHasService('runroom.form_handler.form_handler', FormHandler::class);
        $this->assertContainerBuilderHasService('runroom.form_handler.event_subscriber.form_render', FormRenderSubscriber::class);
    }

    protected function getContainerExtensions(): array
    {
        return [new RunroomFormHandlerExtension()];
    }
}
