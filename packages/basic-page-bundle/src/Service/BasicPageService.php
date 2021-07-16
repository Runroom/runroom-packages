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

use Runroom\BasicPageBundle\Repository\BasicPageRepository;
use Runroom\BasicPageBundle\ViewModel\BasicPageViewModel;

/** @final */
class BasicPageService
{
    private BasicPageRepository $repository;

    public function __construct(BasicPageRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getBasicPageViewModel(string $slug): BasicPageViewModel
    {
        $basicPage = $this->repository->findBySlug($slug);

        $model = new BasicPageViewModel();
        $model->setBasicPage($basicPage);

        return $model;
    }
}
