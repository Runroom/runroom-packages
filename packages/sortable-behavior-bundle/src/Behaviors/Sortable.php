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

namespace Runroom\SortableBehaviorBundle\Behaviors;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait Sortable
{
    /**
     * @var int|null
     *
     * @Gedmo\SortablePosition
     * @ORM\Column(type="integer")
     */
    private $position;

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }
}
