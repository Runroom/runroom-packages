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
use Runroom\BasicPageBundle\Repository\BasicPageRepository;
use Twig\Extension\RuntimeExtensionInterface;

class BasicPageRuntime implements RuntimeExtensionInterface
{
    private BasicPageRepository $repository;

    public function __construct(BasicPageRepository $repository)
    {
        $this->repository = $repository;
    }

    /** @return BasicPage[] */
    public function getBasicPages(?string $location = null): array
    {
        $criteria = ['publish' => true];

        if (null !== $location) {
            $criteria['location'] = $location;
        }

        return $this->repository->findBy($criteria);
    }
}
