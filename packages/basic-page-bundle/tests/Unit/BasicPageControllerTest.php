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
use Runroom\BasicPageBundle\Service\BasicPageService;
use Runroom\BasicPageBundle\ViewModel\BasicPageViewModel;
use Symfony\Component\DependencyInjection\Container;
use Twig\Environment;

class BasicPageControllerTest extends TestCase
{
    /** @var MockObject&BasicPageService */
    private $service;

    /** @var MockObject&Environment */
    private $twig;

    private BasicPageController $controller;

    /** @psalm-suppress InternalMethod setContainer is internal on Symfony 5.x */
    protected function setUp(): void
    {
        $this->service = $this->createMock(BasicPageService::class);
        $this->twig = $this->createMock(Environment::class);

        $container = new Container();
        $container->set('twig', $this->twig);

        $this->controller = new BasicPageController($this->service);
        $this->controller->setContainer($container);
    }

    /** @test */
    public function itRendersStatic(): void
    {
        $model = new BasicPageViewModel();

        $this->service->method('getBasicPageViewModel')->with('slug')->willReturn($model);

        $response = $this->controller->show('slug');

        self::assertSame(200, $response->getStatusCode());
    }
}
