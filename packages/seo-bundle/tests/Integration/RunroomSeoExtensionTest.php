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

class RunroomSeoExtensionTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->load([
            'class' => ['media' => Media::class],
            'locales' => ['es'],
            'xdefault_locale' => 'es',
        ]);
    }

    /** @test */
    public function itHasCoreServicesAlias(): void
    {
        $this->assertContainerBuilderHasService(EntityMetaInformationAdmin::class);
        $this->assertContainerBuilderHasService(MetaInformationAdmin::class);
        $this->assertContainerBuilderHasService(AlternateLinksBuilder::class);
        $this->assertContainerBuilderHasService(AlternateLinksService::class);
        $this->assertContainerBuilderHasService(DefaultAlternateLinksProvider::class);
        $this->assertContainerBuilderHasService(MetaInformationBuilder::class);
        $this->assertContainerBuilderHasService(MetaInformationService::class);
        $this->assertContainerBuilderHasService(DefaultMetaInformationProvider::class);
        $this->assertContainerBuilderHasService(MetaInformationRepository::class);
        $this->assertContainerBuilderHasParameter(RunroomSeoExtension::XDEFAULT_LOCALE, 'es');
    }

    protected function getContainerExtensions(): array
    {
        return [new RunroomSeoExtension()];
    }
}
