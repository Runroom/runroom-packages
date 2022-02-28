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
    /**
     * @var mixed
     */
    private $content;

    /**
     * @var array<string, mixed>
     */
    private array $context = [];

    public function setContent($content): void
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $value
     */
    public function addContext(string $key, $value): void
    {
        $this->context[$key] = $value;
    }

    /**
     * @return mixed
     */
    public function getContext(string $key)
    {
        return $this->context[$key] ?? null;
    }
}
