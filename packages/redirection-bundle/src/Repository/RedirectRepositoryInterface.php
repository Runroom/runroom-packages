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

namespace Runroom\RedirectionBundle\Repository;

interface RedirectRepositoryInterface
{
    /**
     * @return array{ destination: string, httpCode: string }|null
     */
    public function findRedirect(string $source): ?array;
}
