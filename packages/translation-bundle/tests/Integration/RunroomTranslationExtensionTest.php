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
use Runroom\TranslationBundle\Twig\TranslationExtension;

final class RunroomTranslationExtensionTest extends AbstractExtensionTestCase
{
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->container->setParameter('kernel.bundles', [
            'SonataAdminBundle' => true,
            'A2lixTranslationFormBundle' => true,
        ]);

        $this->load();
    }

    public function testItHasCoreServicesAlias(): void
    {
        $this->assertContainerBuilderHasService('runroom.translation.admin.translation', TranslationAdmin::class);
        $this->assertContainerBuilderHasService('runroom.translation.service.translation', TranslationService::class);
        $this->assertContainerBuilderHasService(TranslationRepository::class);
        $this->assertContainerBuilderHasService('runroom.translation.twig.translation', TranslationExtension::class);
    }

    protected function getContainerExtensions(): array
    {
        return [new RunroomTranslationExtension()];
    }
}
