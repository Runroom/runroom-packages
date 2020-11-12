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

namespace Runroom\BasicPageBundle\ViewModel;

use Runroom\BasicPageBundle\Entity\BasicPage;
use Runroom\SeoBundle\Model\SeoModelInterface;

final class BasicPageViewModel implements SeoModelInterface
{
    private ?BasicPage $basicPage = null;

    public function setBasicPage(BasicPage $basicPage): void
    {
        $this->basicPage = $basicPage;
    }

    public function getBasicPage(): ?BasicPage
    {
        return $this->basicPage;
    }
}
