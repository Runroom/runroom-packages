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

namespace Runroom\SeoBundle\MetaInformation;

use Runroom\SeoBundle\Entity\EntityMetaInformation;
use Sonata\MediaBundle\Model\MediaInterface;

interface MetaInformationProviderInterface
{
    public function providesMetas(string $route): bool;

    public function getRouteAlias(string $route): string;

    public function getPlaceholders($model): array;

    public function getEntityMetaInformation($model): ?EntityMetaInformation;

    public function getEntityMetaImage($model): ?MediaInterface;
}
