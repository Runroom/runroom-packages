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

namespace Runroom\SeoBundle\AlternateLinks;

interface AlternateLinksServiceInterface
{
    /**
     * @param array<string, mixed> $context
     *
     * @return array<string, string>
     */
    public function build(array $context): array;
}
