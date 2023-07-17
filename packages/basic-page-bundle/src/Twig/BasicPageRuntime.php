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

namespace Runroom\BasicPageBundle\Twig;

use Runroom\BasicPageBundle\Entity\BasicPage;
use Runroom\BasicPageBundle\Repository\BasicPageRepositoryInterface;
use Twig\Extension\RuntimeExtensionInterface;

final class BasicPageRuntime implements RuntimeExtensionInterface
{
    public function __construct(private readonly BasicPageRepositoryInterface $repository)
    {
    }

    /**
     * @return BasicPage[]
     */
    public function getBasicPages(?string $location = null): array
    {
        return $this->repository->findPublished($location);
    }
}
