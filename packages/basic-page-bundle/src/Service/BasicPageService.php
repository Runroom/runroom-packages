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

namespace Runroom\BasicPageBundle\Service;

use Runroom\BasicPageBundle\Repository\BasicPageRepositoryInterface;
use Runroom\BasicPageBundle\ViewModel\BasicPageViewModel;

final class BasicPageService implements BasicPageServiceInterface
{
    public function __construct(private readonly BasicPageRepositoryInterface $repository)
    {
    }

    public function getBasicPageViewModel(string $slug): BasicPageViewModel
    {
        return new BasicPageViewModel($this->repository->findBySlug($slug));
    }
}
