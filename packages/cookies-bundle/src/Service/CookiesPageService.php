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
use Runroom\FormHandlerBundle\FormHandler;
use Runroom\FormHandlerBundle\ViewModel\FormAwareInterface;

class CookiesPageService
{
    /** @var int */
    private const COOKIES_PAGE_ID = 1;

    /** @var CookiesPageRepository */
    private $repository;

    /** @var FormHandler */
    private $handler;

    /** @var array<string, array{ name: string, has_description?: bool, cookies: string[]}[]> */
    private $cookies;

    /** @param array<string, array{ name: string, has_description?: bool, cookies: string[]}[]> $cookies */
    public function __construct(
        CookiesPageRepository $repository,
        FormHandler $handler,
        array $cookies
    ) {
        $this->repository = $repository;
        $this->handler = $handler;
        $this->cookies = $cookies;
    }

    public function getViewModel(): FormAwareInterface
    {
        $cookiesPage = $this->repository->find(self::COOKIES_PAGE_ID);

        $viewModel = new CookiesPageViewModel();

        if (null !== $cookiesPage) {
            $viewModel->setCookiesPage($cookiesPage);
        }

        $viewModel->setCookies($this->cookies);

        return $this->handler->handleForm(CookiesFormType::class, [], $viewModel);
    }
}
