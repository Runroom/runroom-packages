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
use Runroom\CookiesBundle\Entity\CookiesPage;
use Runroom\CookiesBundle\Form\Type\CookiesFormType;
use Runroom\CookiesBundle\Repository\CookiesPageRepository;
use Runroom\CookiesBundle\Service\CookiesPageService;
use Runroom\CookiesBundle\ViewModel\CookiesPageViewModel;
use Runroom\FormHandlerBundle\FormHandler;

class CookiesPageServiceTest extends TestCase
{
    /** @var MockObject&CookiesPageRepository */
    private $repository;

    /** @var MockObject&FormHandler */
    private $handler;

    /** @var CookiesPageService */
    private $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(CookiesPageRepository::class);
        $this->handler = $this->createMock(FormHandler::class);

        $this->service = new CookiesPageService(
            $this->repository,
            $this->handler,
            []
        );
    }

    /** @test */
    public function itGetsViewModel(): void
    {
        $cookiesPage = new CookiesPage();
        $this->repository->expects(self::once())->method('find')->with(1)->willReturn($cookiesPage);

        $this->handler->expects(self::once())->method('handleForm')
            ->with(CookiesFormType::class, [], self::isInstanceOf(CookiesPageViewModel::class))
            ->willReturnArgument(2);

        $viewModel = $this->service->getViewModel();

        self::assertInstanceOf(CookiesPageViewModel::class, $viewModel);
        self::assertSame($viewModel->getCookiesPage(), $cookiesPage);
        self::assertSame($viewModel->getCookies(), []);
    }
}
