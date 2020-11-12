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
use Runroom\SeoBundle\Model\SeoModelInterface;
use Sonata\MediaBundle\Model\MediaInterface;

/** @phpstan-template T of SeoModelInterface */
interface MetaInformationProviderInterface
{
    public function providesMetas(string $route): bool;

    public function getRouteAlias(string $route): string;

    /** @phpstan-param T $model */
    public function getEntityMetaInformation(SeoModelInterface $model): ?EntityMetaInformation;

    /** @phpstan-param T $model */
    public function getEntityMetaImage(SeoModelInterface $model): ?MediaInterface;
}
