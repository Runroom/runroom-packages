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
    /**
     * @param mixed $content
     */
    public function setContent($content): void;

    /**
     * @return mixed
     */
    public function getContent();

    /**
     * @param mixed $value
     */
    public function addContext(string $key, $value): void;

    /**
     * @return mixed
     */
    public function getContext(string $key);
}
