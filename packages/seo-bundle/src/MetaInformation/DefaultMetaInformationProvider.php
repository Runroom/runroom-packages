<?php

/*
 * This file is part of the Runroom package.
 *
 * (c) Runroom <runroom@runroom.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Runroom\SeoBundle\MetaInformation;

class DefaultMetaInformationProvider extends AbstractMetaInformationProvider
{
    public function providesMetas(string $route): bool
    {
        return true;
    }

    protected function getRoutes(): array
    {
        return [];
    }
}
