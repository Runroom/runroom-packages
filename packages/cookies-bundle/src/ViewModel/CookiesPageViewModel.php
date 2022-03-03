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

namespace Runroom\CookiesBundle\ViewModel;

use Runroom\CookiesBundle\Entity\CookiesPage;
use Symfony\Component\Form\FormView;

/**
 * @phpstan-import-type CookiesData from \Runroom\CookiesBundle\DependencyInjection\Configuration
 */
final class CookiesPageViewModel
{
    private ?CookiesPage $cookiesPage = null;

    /**
     * @phpstan-var CookiesData
     */
    private array $cookies = [];

    private ?FormView $formView = null;

    public function setCookiesPage(CookiesPage $cookiesPage): self
    {
        $this->cookiesPage = $cookiesPage;

        return $this;
    }

    public function getCookiesPage(): ?CookiesPage
    {
        return $this->cookiesPage;
    }

    /**
     * @phpstan-param CookiesData $cookies
     */
    public function setCookies(array $cookies): self
    {
        $this->cookies = $cookies;

        return $this;
    }

    /**
     * @phpstan-return CookiesData
     */
    public function getCookies(): array
    {
        return $this->cookies;
    }

    public function setFormView(FormView $formView): self
    {
        $this->formView = $formView;

        return $this;
    }

    public function getFormView(): ?FormView
    {
        return $this->formView;
    }
}
