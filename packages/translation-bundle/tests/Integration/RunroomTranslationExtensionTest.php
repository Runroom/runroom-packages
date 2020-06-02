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

namespace Runroom\TranslationBundle\Tests\Integration;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Runroom\TranslationBundle\Admin\TranslationAdmin;
use Runroom\TranslationBundle\DependencyInjection\RunroomTranslationExtension;
use Runroom\TranslationBundle\Repository\TranslationRepository;
use Runroom\TranslationBundle\Service\TranslationService;
use Runroom\TranslationBundle\Twig\TranslationExtension as TwigTranslationExtension;

class RunroomTranslationExtensionTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->load();
    }

    /** @test */
    public function itHasCoreServicesAlias(): void
    {
        $this->assertContainerBuilderHasService(TranslationAdmin::class);
        $this->assertContainerBuilderHasService(TranslationService::class);
        $this->assertContainerBuilderHasService(TranslationRepository::class);
        $this->assertContainerBuilderHasService(TwigTranslationExtension::class);
    }

    protected function getContainerExtensions(): array
    {
        return [new RunroomTranslationExtension()];
    }
}
