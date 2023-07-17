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
use Runroom\BasicPageBundle\Twig\BasicPageExtension;
use Runroom\BasicPageBundle\Twig\BasicPageRuntime;

final class RunroomBasicPageExtensionTest extends AbstractExtensionTestCase
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

    public function testItHasCoreServicesAlias(): void
    {
        $this->assertContainerBuilderHasService('runroom.basic_page.admin.basic_page', BasicPageAdmin::class);
        $this->assertContainerBuilderHasService('runroom.basic_page.controller.basic_page', BasicPageController::class);
        $this->assertContainerBuilderHasService('runroom.basic_page.service.basic_page', BasicPageService::class);
        $this->assertContainerBuilderHasService('runroom.basic_page.service.basic_page_alternate_links', BasicPageAlternateLinksProvider::class);
        $this->assertContainerBuilderHasService('runroom.basic_page.service.basic_page_meta_information', BasicPageMetaInformationProvider::class);
        $this->assertContainerBuilderHasService(BasicPageRepository::class);
        $this->assertContainerBuilderHasService('runroom.basic_page.twig.basic_page', BasicPageExtension::class);
        $this->assertContainerBuilderHasService('runroom.basic_page.twig.basic_page.runtime', BasicPageRuntime::class);
    }

    protected function getContainerExtensions(): array
    {
        return [new RunroomBasicPageExtension()];
    }
}
