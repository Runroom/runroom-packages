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

namespace Runroom\SeoBundle\Tests\Integration;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Runroom\SeoBundle\Admin\EntityMetaInformationAdmin;
use Runroom\SeoBundle\Admin\MetaInformationAdmin;
use Runroom\SeoBundle\AlternateLinks\AlternateLinksBuilder;
use Runroom\SeoBundle\AlternateLinks\AlternateLinksService;
use Runroom\SeoBundle\AlternateLinks\DefaultAlternateLinksProvider;
use Runroom\SeoBundle\DependencyInjection\RunroomSeoExtension;
use Runroom\SeoBundle\MetaInformation\DefaultMetaInformationProvider;
use Runroom\SeoBundle\MetaInformation\MetaInformationBuilder;
use Runroom\SeoBundle\MetaInformation\MetaInformationService;
use Runroom\SeoBundle\Repository\MetaInformationRepository;
use Runroom\SeoBundle\Tests\App\Entity\Media;
use Runroom\SeoBundle\Twig\SeoExtension;
use Runroom\SeoBundle\Twig\SeoRuntime;

class RunroomSeoExtensionTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->container->setParameter('kernel.bundles', [
            'SonataAdminBundle' => true,
            'A2lixTranslationFormBundle' => true,
        ]);

        $this->load([
            'class' => ['media' => Media::class],
            'locales' => ['es'],
            'xdefault_locale' => 'es',
        ]);
    }

    /**
     * @test
     */
    public function itHasCoreServicesAlias(): void
    {
        $this->assertContainerBuilderHasService('runroom.seo.admin.entity_meta_information', EntityMetaInformationAdmin::class);
        $this->assertContainerBuilderHasService('runroom.seo.admin.meta_information', MetaInformationAdmin::class);
        $this->assertContainerBuilderHasService('runroom.seo.alternate_links.builder', AlternateLinksBuilder::class);
        $this->assertContainerBuilderHasService('runroom.seo.alternate_links.service', AlternateLinksService::class);
        $this->assertContainerBuilderHasService('runroom.seo.alternate_links.default_provider', DefaultAlternateLinksProvider::class);
        $this->assertContainerBuilderHasService('runroom.seo.meta_information.builder', MetaInformationBuilder::class);
        $this->assertContainerBuilderHasService('runroom.seo.meta_information.service', MetaInformationService::class);
        $this->assertContainerBuilderHasService('runroom.seo.meta_information.default_provider', DefaultMetaInformationProvider::class);
        $this->assertContainerBuilderHasService(MetaInformationRepository::class);
        $this->assertContainerBuilderHasService('runroom.seo.twig.seo', SeoExtension::class);
        $this->assertContainerBuilderHasService('runroom.seo.twig.seo_runtime', SeoRuntime::class);
        $this->assertContainerBuilderHasParameter(RunroomSeoExtension::XDEFAULT_LOCALE, 'es');
    }

    protected function getContainerExtensions(): array
    {
        return [new RunroomSeoExtension()];
    }
}
