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

use Runroom\BasicPageBundle\ViewModel\BasicPageViewModel;
use Runroom\SeoBundle\Entity\EntityMetaInformation;
use Runroom\SeoBundle\MetaInformation\AbstractMetaInformationProvider;
use Runroom\SeoBundle\Model\SeoModelInterface;

/** @phpstan-extends AbstractMetaInformationProvider<BasicPageViewModel> */
final class BasicPageMetaInformationProvider extends AbstractMetaInformationProvider
{
    public function getEntityMetaInformation(SeoModelInterface $model): ?EntityMetaInformation
    {
        $basicPage = $model->getBasicPage();

        return null !== $basicPage ? $basicPage->getMetaInformation() : null;
    }

    protected function getRoutes(): array
    {
        return ['runroom.basic_page.route.show'];
    }
}
