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

final class PageViewModel implements PageViewModelInterface
{
    private mixed $content = null;

    /**
     * @var array<string, mixed>
     */
    private array $context = [];

    public function setContent(mixed $content): void
    {
        $this->content = $content;
    }

    public function getContent(): mixed
    {
        return $this->content;
    }

    public function addContext(string $key, mixed $value): void
    {
        $this->context[$key] = $value;
    }

    public function getContext(string $key): mixed
    {
        return $this->context[$key] ?? null;
    }
}
