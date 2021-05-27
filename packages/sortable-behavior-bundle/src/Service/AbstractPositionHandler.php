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

namespace Runroom\SortableBehaviorBundle\Service;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

abstract class AbstractPositionHandler implements PositionHandlerInterface
{
    private PropertyAccessorInterface $propertyAccessor;

    abstract public function getLastPosition(object $entity): int;

    abstract public function getPositionFieldByEntity($entity): string;

    public function setPropertyAccessor(PropertyAccessorInterface $propertyAccessor): self
    {
        $this->propertyAccessor = $propertyAccessor;

        return $this;
    }

    public function getPropertyAccessor(): PropertyAccessorInterface
    {
        return $this->propertyAccessor;
    }

    public function getCurrentPosition(object $entity): int
    {
        return $this->getPropertyAccessor()->getValue($entity, $this->getPositionFieldByEntity($entity));
    }

    public function getPosition(object $entity, string $movePosition, int $lastPosition): int
    {
        $newPosition = $this->getCurrentPosition($entity);

        switch ($movePosition) {
            case 'up':
                --$newPosition;
                break;
            case 'down':
                ++$newPosition;
                break;
            case 'top':
                $newPosition = 0;
                break;
            case 'bottom':
                $newPosition = $lastPosition;
                break;
        }

        return max(0, min($newPosition, $lastPosition));
    }
}
