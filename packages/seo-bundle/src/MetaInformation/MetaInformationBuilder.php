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
use Runroom\SeoBundle\Entity\MetaInformation;
use Runroom\SeoBundle\Repository\MetaInformationRepository;
use Runroom\SeoBundle\ViewModel\MetaInformationViewModel;

/** @final */
class MetaInformationBuilder
{
    private const DEFAULT_ROUTE = 'default';

    /** @var MetaInformationRepository */
    private $repository;

    public function __construct(MetaInformationRepository $repository)
    {
        $this->repository = $repository;
    }

    /** @param mixed $model */
    public function build(MetaInformationProviderInterface $provider, string $route, $model): MetaInformationViewModel
    {
        $routeMetas = $this->getMetasForRoute($provider, $route);
        $modelMetas = $provider->getEntityMetaInformation($model);
        $modelImage = $provider->getEntityMetaImage($model);
        $placeholders = $provider->getPlaceholders($model);

        $metas = new MetaInformationViewModel();
        $metas->setTitle(strtr($this->getTitle($modelMetas, $routeMetas), $placeholders));
        $metas->setDescription(strtr($this->getDescription($modelMetas, $routeMetas), $placeholders));
        $metas->setImage($modelImage ?? $routeMetas->getImage());

        return $metas;
    }

    private function getMetasForRoute(MetaInformationProviderInterface $provider, string $route): MetaInformation
    {
        return $this->repository->findOneBy(['route' => $provider->getRouteAlias($route)]) ??
            $this->repository->findOneBy(['route' => self::DEFAULT_ROUTE]);
    }

    private function getTitle(?EntityMetaInformation $modelMetas, MetaInformation $routeMetas): string
    {
        $title = null !== $modelMetas ? $modelMetas->getTitle() : null;

        return (string) ($title ?? $routeMetas->getTitle());
    }

    private function getDescription(?EntityMetaInformation $modelMetas, MetaInformation $routeMetas): string
    {
        $description = null !== $modelMetas ? $modelMetas->getDescription() : null;

        return (string) ($description ?? $routeMetas->getDescription());
    }
}
