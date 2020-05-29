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

namespace Runroom\SortableBehaviorBundle\Services;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;

final class ORMPositionHandler extends AbstractPositionHandler
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var array */
    private $positionField;

    /** @var array */
    private $sortableGroups;

    /** @var array */
    private static $cacheLastPosition = [];

    public function __construct(
        EntityManagerInterface $entityManager,
        array $positionField,
        array $sortableGroups
    ) {
        $this->entityManager = $entityManager;
        $this->positionField = $positionField;
        $this->sortableGroups = $sortableGroups;
    }

    public function getLastPosition(object $entity): int
    {
        $entityClass = ClassUtils::getClass($entity);
        $parentEntityClass = true;

        while ($parentEntityClass) {
            $parentEntityClass = ClassUtils::getParentClass($entityClass);

            if (class_exists($parentEntityClass)) {
                $reflection = new \ReflectionClass($parentEntityClass);

                if ($reflection->isAbstract()) {
                    break;
                }

                $entityClass = $parentEntityClass;
            }
        }

        $groups = $this->getSortableGroupsFieldByEntity($entityClass);
        $cacheKey = $this->getCacheKeyForLastPosition($entity, $groups);

        if (!isset(self::$cacheLastPosition[$cacheKey])) {
            $queryBuilder = $this->entityManager->createQueryBuilder()
                ->select(sprintf('MAX(t.%s) as last_position', $this->getPositionFieldByEntity($entityClass)))
                ->from($entityClass, 't');

            if ($groups) {
                $index = 1;

                foreach ($groups as $groupName) {
                    $getter = 'get' . $groupName;

                    if ($entity->$getter()) {
                        $queryBuilder
                            ->andWhere(sprintf('t.%s = :group_%s', $groupName, $index))
                            ->setParameter(sprintf('group_%s', $index), $entity->$getter())
                        ;
                        ++$index;
                    }
                }
            }

            self::$cacheLastPosition[$cacheKey] = (int) $queryBuilder->getQuery()->getSingleScalarResult();
        }

        return self::$cacheLastPosition[$cacheKey];
    }

    public function getPositionFieldByEntity($entity): string
    {
        if (\is_object($entity)) {
            $entity = ClassUtils::getClass($entity);
        }

        if (isset($this->positionField['entities'][$entity])) {
            return $this->positionField['entities'][$entity];
        }

        return $this->positionField['default'];
    }

    private function getSortableGroupsFieldByEntity(string $entity): array
    {
        $groups = [];

        if (isset($this->sortableGroups['entities'][$entity])) {
            $groups = $this->sortableGroups['entities'][$entity];
        }

        return $groups;
    }

    private function getCacheKeyForLastPosition(object $entity, array $groups): string
    {
        $cacheKey = ClassUtils::getClass($entity);

        foreach ($groups as $groupName) {
            $getter = 'get' . $groupName;

            if ($entity->$getter()) {
                $cacheKey .= '_' . $entity->$getter()->getId();
            }
        }

        return $cacheKey;
    }
}
