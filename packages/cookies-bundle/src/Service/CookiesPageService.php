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

use Doctrine\Persistence\ObjectRepository;
use Runroom\CookiesBundle\DependencyInjection\Configuration;
use Runroom\CookiesBundle\Entity\CookiesPage;
use Runroom\CookiesBundle\Form\Type\CookiesFormType;
use Runroom\CookiesBundle\ViewModel\CookiesPageViewModel;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * @phpstan-import-type CookiesData from Configuration
 */
final readonly class CookiesPageService implements CookiesPageServiceInterface
{
    private const int COOKIES_PAGE_ID = 1;

    /**
     * @param ObjectRepository<CookiesPage> $repository
     *
     * @phpstan-param CookiesData $cookies
     */
    public function __construct(
        private ObjectRepository $repository,
        private FormFactoryInterface $formFactory,
        private array $cookies,
    ) {}

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
