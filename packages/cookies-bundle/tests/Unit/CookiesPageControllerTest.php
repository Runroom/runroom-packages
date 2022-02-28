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

namespace Runroom\CookiesBundle\Tests\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Runroom\CookiesBundle\Controller\CookiesPageController;
use Runroom\CookiesBundle\Service\CookiesPageService;
use Runroom\CookiesBundle\ViewModel\CookiesPageViewModel;
use Symfony\Component\DependencyInjection\Container;
use Twig\Environment;

class CookiesPageControllerTest extends TestCase
{
    /**
     * @var MockObject&CookiesPageService
     */
    private $service;

    /**
     * @var MockObject&Environment
     */
    private $twig;

    private CookiesPageController $controller;

    protected function setUp(): void
    {
        $this->service = $this->createMock(CookiesPageService::class);
        $this->twig = $this->createMock(Environment::class);

        $container = new Container();
        $container->set('twig', $this->twig);

        $this->controller = new CookiesPageController($this->service);
        $this->controller->setContainer($container);
    }

    /**
     * @test
     */
    public function itRendersCookiesPage(): void
    {
        $model = new CookiesPageViewModel();

        $this->service->expects(static::once())->method('getCookiesPageViewModel')->willReturn($model);
        $this->twig->expects(static::once())->method('render')->with('@RunroomCookies/show.html.twig', ['model' => $model])->willReturn('rendered');

        $response = $this->controller->index();

        static::assertSame(200, $response->getStatusCode());
    }
}
