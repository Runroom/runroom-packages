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

namespace Runroom\BasicPageBundle\Tests\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Runroom\BasicPageBundle\Controller\BasicPageController;
use Runroom\BasicPageBundle\Entity\BasicPage;
use Runroom\BasicPageBundle\Service\BasicPageService;
use Runroom\BasicPageBundle\ViewModel\BasicPageViewModel;
use Symfony\Component\DependencyInjection\Container;
use Twig\Environment;

class BasicPageControllerTest extends TestCase
{
    /**
     * @var MockObject&BasicPageService
     */
    private $service;

    /**
     * @var MockObject&Environment
     */
    private $twig;

    private BasicPageController $controller;

    protected function setUp(): void
    {
        $this->service = $this->createMock(BasicPageService::class);
        $this->twig = $this->createMock(Environment::class);

        $container = new Container();
        $container->set('twig', $this->twig);

        $this->controller = new BasicPageController($this->service);
        $this->controller->setContainer($container);
    }

    public function testItRendersStatic(): void
    {
        $model = new BasicPageViewModel(new BasicPage());

        $this->service->expects(static::once())->method('getBasicPageViewModel')->with('slug')->willReturn($model);
        $this->twig->expects(static::once())->method('render')->with('@RunroomBasicPage/show.html.twig', ['model' => $model])->willReturn('rendered');

        $response = $this->controller->show('slug');

        static::assertSame(200, $response->getStatusCode());
    }
}
