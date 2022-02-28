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

namespace Runroom\CookiesBundle\Tests\Integration;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Runroom\CookiesBundle\Admin\CookiesPageAdmin;
use Runroom\CookiesBundle\Controller\CookiesPageController;
use Runroom\CookiesBundle\DependencyInjection\RunroomCookiesExtension;
use Runroom\CookiesBundle\Repository\CookiesPageRepository;
use Runroom\CookiesBundle\Service\CookiesPageService;
use Runroom\CookiesBundle\Twig\CookiesExtension;
use Runroom\CookiesBundle\Twig\CookiesRuntime;

class RunroomCookiesExtensionTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->container->setParameter('kernel.bundles', [
            'SonataAdminBundle' => true,
            'FOSCKEditorBundle' => true,
            'A2lixTranslationFormBundle' => true,
        ]);

        $this->load(['cookies' => [
            'mandatory_cookies' => [[
                'name' => 'test', 'cookies' => [['name' => 'test']],
            ]],
            'performance_cookies' => [[
                'name' => 'test', 'cookies' => [['name' => 'test']],
            ]],
            'targeting_cookies' => [[
                'name' => 'test', 'cookies' => [['name' => 'test']],
            ]],
        ]]);
    }

    /**
     * @test
     */
    public function itHasCoreServicesAlias(): void
    {
        $this->assertContainerBuilderHasService(CookiesPageAdmin::class);
        $this->assertContainerBuilderHasService(CookiesPageController::class);
        $this->assertContainerBuilderHasService(CookiesPageService::class);
        $this->assertContainerBuilderHasService(CookiesPageRepository::class);
        $this->assertContainerBuilderHasService(CookiesExtension::class);
        $this->assertContainerBuilderHasService(CookiesRuntime::class);
    }

    protected function getContainerExtensions(): array
    {
        return [new RunroomCookiesExtension()];
    }
}
