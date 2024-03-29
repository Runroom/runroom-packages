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

interface PageViewModelInterface
{
    public function setContent(mixed $content): void;

    public function getContent(): mixed;

    public function addContext(string $key, mixed $value): void;

    public function getContext(string $key): mixed;
}
