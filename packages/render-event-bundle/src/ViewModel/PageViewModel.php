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

namespace Runroom\RenderEventBundle\ViewModel;

class PageViewModel implements PageViewModelInterface
{
    private $content;
    private $context = [];

    public function setContent($content): void
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function addContext(string $key, $value): void
    {
        $this->context[$key] = $value;
    }

    public function getContext(string $key)
    {
        return $this->context[$key] ?? null;
    }
}
