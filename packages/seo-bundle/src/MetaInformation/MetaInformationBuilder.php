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
use Runroom\SeoBundle\Model\SeoModelInterface;
use Runroom\SeoBundle\Repository\MetaInformationRepository;
use Runroom\SeoBundle\ViewModel\MetaInformationViewModel;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

use function Symfony\Component\String\s;

/** @final */
class MetaInformationBuilder
{
    public const DEFAULT_ROUTE = 'default';

    private MetaInformationRepository $repository;
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(
        MetaInformationRepository $repository,
        PropertyAccessorInterface $propertyAccessor
    ) {
        $this->repository = $repository;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @phpstan-template T of SeoModelInterface
     *
     * @phpstan-param MetaInformationProviderInterface<T> $provider
     * @phpstan-param T $model
     */
    public function build(
        MetaInformationProviderInterface $provider,
        SeoModelInterface $model,
        string $route
    ): MetaInformationViewModel {
        $routeMetas = $this->getMetasForRoute($provider, $route);
        $modelMetas = $provider->getEntityMetaInformation($model);
        $modelImage = $provider->getEntityMetaImage($model);

        $metas = new MetaInformationViewModel();
        $metas->setTitle($this->replacePlaceholders($model, $this->getTitle($modelMetas, $routeMetas)));
        $metas->setDescription($this->replacePlaceholders($model, $this->getDescription($modelMetas, $routeMetas)));
        $metas->setImage($modelImage ?? $routeMetas->getImage());

        return $metas;
    }

    /** @phpstan-param MetaInformationProviderInterface<SeoModelInterface> $provider */
    private function getMetasForRoute(MetaInformationProviderInterface $provider, string $route): MetaInformation
    {
        return $this->repository->findOneBy(['route' => $provider->getRouteAlias($route)]) ??
            $this->repository->findOneBy(['route' => self::DEFAULT_ROUTE]) ?? new MetaInformation();
    }

    private function getTitle(?EntityMetaInformation $contextMetas, MetaInformation $routeMetas): string
    {
        $title = null !== $contextMetas ? $contextMetas->getTitle() : null;

        return (string) ($title ?? $routeMetas->getTitle());
    }

    private function getDescription(?EntityMetaInformation $contextMetas, MetaInformation $routeMetas): string
    {
        $description = null !== $contextMetas ? $contextMetas->getDescription() : null;

        return (string) ($description ?? $routeMetas->getDescription());
    }

    private function replacePlaceholders(SeoModelInterface $model, string $text): string
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
