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

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Runroom\CookiesBundle\Entity\CookiesPage;
use Runroom\CookiesBundle\Form\Type\CookiesFormType;
use Runroom\CookiesBundle\Repository\CookiesPageRepository;
use Runroom\CookiesBundle\Service\CookiesPageService;
use Runroom\CookiesBundle\ViewModel\CookiesPageViewModel;
use Runroom\FormHandlerBundle\FormHandler;

class CookiesPageServiceTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<CookiesPageRepository> */
    private $repository;

    /** @var ObjectProphecy<FormHandler> */
    private $handler;

    /** @var CookiesPageService */
    private $service;

    protected function setUp(): void
    {
        $this->repository = $this->prophesize(CookiesPageRepository::class);
        $this->handler = $this->prophesize(FormHandler::class);

        $this->service = new CookiesPageService(
            $this->repository->reveal(),
            $this->handler->reveal(),
            []
        );
    }

    /** @test */
    public function itGetsViewModel(): void
    {
        $cookiesPage = new CookiesPage();
        $this->repository->find(1)->shouldBeCalled()->willReturn($cookiesPage);

        $this->handler->handleForm(CookiesFormType::class, [], Argument::type(CookiesPageViewModel::class))
            ->shouldBeCalled()
            ->willReturnArgument(2);

        $viewModel = $this->service->getViewModel();

        self::assertInstanceOf(CookiesPageViewModel::class, $viewModel);
        self::assertSame($viewModel->getCookiesPage(), $cookiesPage);
        self::assertSame($viewModel->getCookies(), []);
    }
}
