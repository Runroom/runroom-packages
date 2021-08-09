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

namespace Runroom\BasicPageBundle\Tests\Integration;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Runroom\BasicPageBundle\Admin\BasicPageAdmin;
use Runroom\BasicPageBundle\Controller\BasicPageController;
use Runroom\BasicPageBundle\DependencyInjection\RunroomBasicPageExtension;
use Runroom\BasicPageBundle\Repository\BasicPageRepository;
use Runroom\BasicPageBundle\Service\BasicPageAlternateLinksProvider;
use Runroom\BasicPageBundle\Service\BasicPageMetaInformationProvider;
use Runroom\BasicPageBundle\Service\BasicPageService;

class RunroomBasicPageExtensionTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->container->setParameter('kernel.bundles', [
            'SonataAdminBundle' => true,
            'FOSCKEditorBundle' => true,
            'A2lixTranslationFormBundle' => true,
        ]);

        $this->load();
    }

    /** @test */
    public function itHasCoreServicesAlias(): void
    {
        $this->assertContainerBuilderHasService(BasicPageAdmin::class);
        $this->assertContainerBuilderHasService(BasicPageController::class);
        $this->assertContainerBuilderHasService(BasicPageService::class);
        $this->assertContainerBuilderHasService(BasicPageAlternateLinksProvider::class);
        $this->assertContainerBuilderHasService(BasicPageMetaInformationProvider::class);
        $this->assertContainerBuilderHasService(BasicPageRepository::class);
    }

    protected function getContainerExtensions(): array
    {
        return [new RunroomBasicPageExtension()];
    }
}
