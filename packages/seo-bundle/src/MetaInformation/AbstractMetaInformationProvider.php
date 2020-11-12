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

/**
 * @phpstan-template T of SeoModelInterface
 * @phpstan-implements MetaInformationProviderInterface<T>
 */
abstract class AbstractMetaInformationProvider implements MetaInformationProviderInterface
{
    public function providesMetas(string $route): bool
    {
        return \in_array($route, $this->getRoutes(), true);
    }

    public function getRouteAlias(string $route): string
    {
        foreach ($this->getRouteAliases() as $alias => $routes) {
            if (\in_array($route, $routes, true)) {
                return $alias;
            }
        }

        return $route;
    }

    public function getEntityMetaInformation(SeoModelInterface $model): ?EntityMetaInformation
    {
        return null;
    }

    public function getEntityMetaImage(SeoModelInterface $model): ?MediaInterface
    {
        return null;
    }

    /** @return array<string, string[]> */
    protected function getRouteAliases(): array
    {
        return [];
    }

    /** @return string[] */
    abstract protected function getRoutes(): array;
}
