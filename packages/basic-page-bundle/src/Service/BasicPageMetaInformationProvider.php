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

final class BasicPageMetaInformationProvider extends AbstractMetaInformationProvider
{
    public function getEntityMetaInformation(array $context): ?EntityMetaInformation
    {
        if (!isset($context['model']) || !$context['model'] instanceof BasicPageViewModel) {
            return null;
        }

        return $context['model']->getBasicPage()->getMetaInformation();
    }

    protected function getRoutes(): array
    {
        return ['runroom.basic_page.route.show'];
    }
}
