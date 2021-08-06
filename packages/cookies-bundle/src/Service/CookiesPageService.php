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

namespace Runroom\CookiesBundle\Service;

use Runroom\CookiesBundle\Form\Type\CookiesFormType;
use Runroom\CookiesBundle\Repository\CookiesPageRepository;
use Runroom\CookiesBundle\ViewModel\CookiesPageViewModel;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * @final
 *
 * @phpstan-import-type CookiesData from \Runroom\CookiesBundle\DependencyInjection\Configuration
 */
class CookiesPageService
{
    private const COOKIES_PAGE_ID = 1;

    private CookiesPageRepository $repository;
    private FormFactoryInterface $formFactory;

    /** @phpstan-var CookiesData */
    private array $cookies;

    /** @phpstan-param CookiesData $cookies */
    public function __construct(
        CookiesPageRepository $repository,
        FormFactoryInterface $formFactory,
        array $cookies
    ) {
        $this->repository = $repository;
        $this->formFactory = $formFactory;
        $this->cookies = $cookies;
    }

    public function getCookiesPageViewModel(): CookiesPageViewModel
    {
        $cookiesPage = $this->repository->find(self::COOKIES_PAGE_ID);

        if (null === $cookiesPage) {
            throw new \RuntimeException('Cookies page not found, did you forget to generate it?');
        }

        $form = $this->formFactory->create(CookiesFormType::class);

        $model = new CookiesPageViewModel();
        $model->setCookiesPage($cookiesPage);
        $model->setFormView($form->createView());
        $model->setCookies($this->cookies);

        return $model;
    }
}
