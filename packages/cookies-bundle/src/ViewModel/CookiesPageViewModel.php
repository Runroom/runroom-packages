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
use Runroom\FormHandlerBundle\ViewModel\FormAware;
use Runroom\FormHandlerBundle\ViewModel\FormAwareInterface;

final class CookiesPageViewModel implements FormAwareInterface
{
    use FormAware;

    private ?CookiesPage $cookiesPage = null;

    /** @var array<string, array{ name: string, has_description?: bool, cookies: string[]}[]> */
    private array $cookies = [];

    public function setCookiesPage(CookiesPage $cookiesPage): self
    {
        $this->cookiesPage = $cookiesPage;

        return $this;
    }

    public function getCookiesPage(): ?CookiesPage
    {
        return $this->cookiesPage;
    }

    /** @param array<string, array{ name: string, has_description?: bool, cookies: string[]}[]> $cookies */
    public function setCookies(array $cookies): self
    {
        $this->cookies = $cookies;

        return $this;
    }

    /** @return array<string, array{ name: string, has_description?: bool, cookies: string[]}[]> */
    public function getCookies(): array
    {
        return $this->cookies;
    }
}
