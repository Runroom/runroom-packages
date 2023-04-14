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

    public function testItHasCoreServicesAlias(): void
    {
        $this->assertContainerBuilderHasService('runroom.cookies.admin.cookies_page', CookiesPageAdmin::class);
        $this->assertContainerBuilderHasService('runroom.cookies.controller.cookies_page', CookiesPageController::class);
        $this->assertContainerBuilderHasService('runroom.cookies.service.cookies_page', CookiesPageService::class);
        $this->assertContainerBuilderHasService(CookiesPageRepository::class);
        $this->assertContainerBuilderHasService('runroom.cookies.twig.cookies', CookiesExtension::class);
        $this->assertContainerBuilderHasService('runroom.cookies.twig.cookies_runtime', CookiesRuntime::class);
    }

    protected function getContainerExtensions(): array
    {
        return [new RunroomCookiesExtension()];
    }
}
