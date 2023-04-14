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
use Runroom\CookiesBundle\Factory\CookiesPageFactory;
use Runroom\CookiesBundle\Form\Type\CookiesFormType;
use Runroom\CookiesBundle\Repository\CookiesPageRepository;
use Runroom\CookiesBundle\Service\CookiesPageService;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Zenstruck\Foundry\Test\Factories;

class CookiesPageServiceTest extends TestCase
{
    use Factories;

    /**
     * @var MockObject&CookiesPageRepository
     */
    private $repository;

    /**
     * @var MockObject&FormFactoryInterface
     */
    private $formFactory;

    private CookiesPageService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(CookiesPageRepository::class);
        $this->formFactory = $this->createMock(FormFactoryInterface::class);

        $this->service = new CookiesPageService(
            $this->repository,
            $this->formFactory,
            []
        );
    }

    public function testItThrowsExceptionIfCoookiesPageNotFound(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cookies page not found, did you forget to generate it?');

        $this->service->getCookiesPageViewModel();
    }

    public function testItGetsCookiesPage(): void
    {
        $cookiesPage = CookiesPageFactory::createOne()->object();
        $form = $this->createStub(FormInterface::class);
        $formView = $this->createStub(FormView::class);

        $form->method('createView')->willReturn($formView);

        $this->repository->expects(static::once())->method('find')->with(1)->willReturn($cookiesPage);
        $this->formFactory->expects(static::once())->method('create')
            ->with(CookiesFormType::class)
            ->willReturn($form);

        $model = $this->service->getCookiesPageViewModel();

        static::assertSame($model->getCookiesPage(), $cookiesPage);
        static::assertSame($model->getFormView(), $formView);
        static::assertSame($model->getCookies(), []);
    }
}
