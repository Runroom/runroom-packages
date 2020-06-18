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

class CookiesViewModel
{
    /** @var array */
    protected $performanceCookies = [];

    /** @var array */
    protected $targetingCookies = [];

    public function setPerformanceCookies(array $performanceCookies): self
    {
        $this->performanceCookies = $performanceCookies;

        return $this;
    }

    public function getPerformanceCookies(): ?array
    {
        return $this->performanceCookies;
    }

    public function setTargetingCookies(array $targetingCookies): self
    {
        $this->targetingCookies = $targetingCookies;

        return $this;
    }

    public function getTargetingCookies(): ?array
    {
        return $this->targetingCookies;
    }
}
