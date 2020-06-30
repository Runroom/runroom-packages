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
    /** @var string[] */
    private $performanceCookies = [];

    /** @var string[] */
    private $targetingCookies = [];

    /** @param string[] $performanceCookies */
    public function setPerformanceCookies(array $performanceCookies): self
    {
        $this->performanceCookies = $performanceCookies;

        return $this;
    }

    /** @return string[] */
    public function getPerformanceCookies(): array
    {
        return $this->performanceCookies;
    }

    /** @param string[] $targetingCookies */
    public function setTargetingCookies(array $targetingCookies): self
    {
        $this->targetingCookies = $targetingCookies;

        return $this;
    }

    /** @return string[] */
    public function getTargetingCookies(): array
    {
        return $this->targetingCookies;
    }
}
