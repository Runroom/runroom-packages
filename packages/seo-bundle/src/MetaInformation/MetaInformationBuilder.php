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
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

use function Symfony\Component\String\s;

/** @final */
class MetaInformationBuilder
{
    public const DEFAULT_ROUTE = 'default';

    /** @var MetaInformationRepository */
    private $repository;

    /** @var PropertyAccessorInterface */
    private $propertyAccessor;

    public function __construct(
        MetaInformationRepository $repository,
        PropertyAccessorInterface $propertyAccessor
    ) {
        $this->repository = $repository;
        $this->propertyAccessor = $propertyAccessor;
    }

    /** @param mixed $model */
    public function build(MetaInformationProviderInterface $provider, string $route, $model): MetaInformationViewModel
    {
        $routeMetas = $this->getMetasForRoute($provider, $route);
        $modelMetas = $provider->getEntityMetaInformation($model);
        $modelImage = $provider->getEntityMetaImage($model);

        $metas = new MetaInformationViewModel();
        $metas->setTitle($this->replacePlaceholders($this->getTitle($modelMetas, $routeMetas), $model));
        $metas->setDescription($this->replacePlaceholders($this->getDescription($modelMetas, $routeMetas), $model));
        $metas->setImage($modelImage ?? $routeMetas->getImage());

        return $metas;
    }

    private function getMetasForRoute(MetaInformationProviderInterface $provider, string $route): MetaInformation
    {
        return $this->repository->findOneBy(['route' => $provider->getRouteAlias($route)]) ??
            $this->repository->findOneBy(['route' => self::DEFAULT_ROUTE]) ?? new MetaInformation();
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

    /** @param mixed $model */
    private function replacePlaceholders(string $text, $model): string
    {
        return (string) s($text)->replaceMatches('/\[(.*)\]/', function (array $match) use ($model): string {
            try {
                return $this->propertyAccessor->getValue($model, $match[1]);
            } catch (NoSuchPropertyException $e) {
            }

            return '';
        });
    }
}
