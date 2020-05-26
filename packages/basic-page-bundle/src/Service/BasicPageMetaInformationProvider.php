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

use Runroom\SeoBundle\Entity\EntityMetaInformation;
use Runroom\SeoBundle\MetaInformation\AbstractMetaInformationProvider;

final class BasicPageMetaInformationProvider extends AbstractMetaInformationProvider
{
    public function getEntityMetaInformation($model): ?EntityMetaInformation
    {
        return $model->getBasicPage()->getMetaInformation();
    }

    public function getPlaceholders($model): array
    {
        return [
            '{title}' => $model->getBasicPage()->getTitle(),
            '{content}' => $model->getBasicPage()->getContent(),
        ];
    }

    protected function getRoutes(): array
    {
        return ['runroom.static_page.route.static'];
    }
}
