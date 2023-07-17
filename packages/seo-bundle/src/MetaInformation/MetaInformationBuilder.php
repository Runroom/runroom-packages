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

use Doctrine\Persistence\ObjectRepository;
use Runroom\SeoBundle\Entity\EntityMetaInformation;
use Runroom\SeoBundle\Entity\MetaInformation;
use Runroom\SeoBundle\ViewModel\MetaInformationViewModel;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

use function Symfony\Component\String\s;

final class MetaInformationBuilder implements MetaInformationBuilderInterface
{
    /**
     * @param ObjectRepository<MetaInformation> $repository
     */
    public function __construct(
        private readonly ObjectRepository $repository,
        private readonly PropertyAccessorInterface $propertyAccessor
    ) {
    }

    public function build(
        MetaInformationProviderInterface $provider,
        array $context,
        string $route
    ): MetaInformationViewModel {
        $routeMetas = $this->getMetasForRoute($provider, $route);
        $modelMetas = $provider->getEntityMetaInformation($context);
        $modelImage = $provider->getEntityMetaImage($context);

        $metas = new MetaInformationViewModel();
        $metas->setTitle($this->replacePlaceholders($context, $this->getTitle($modelMetas, $routeMetas)));
        $metas->setDescription($this->replacePlaceholders($context, $this->getDescription($modelMetas, $routeMetas)));
        $metas->setImage($modelImage ?? $routeMetas->getImage());

        return $metas;
    }

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

    /**
     * @param array<string, mixed> $context
     */
    private function replacePlaceholders(array $context, string $text): string
    {
        $contextObject = (object) $context;

        return (string) s($text)->replaceMatches('/\[(.*)\]/', function (array $match) use ($contextObject): string {
            try {
                $value = $this->propertyAccessor->getValue($contextObject, $match[1]);

                if (!\is_string($value)) {
                    return '';
                }

                return $value;
            } catch (NoSuchPropertyException) {
            }

            return '';
        });
    }
}
