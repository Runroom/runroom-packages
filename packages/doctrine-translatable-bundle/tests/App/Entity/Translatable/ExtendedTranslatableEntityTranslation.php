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

namespace Runroom\DoctrineTranslatableBundle\Tests\App\Entity\Translatable;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;

#[Entity]
class ExtendedTranslatableEntityTranslation extends AbstractTranslatableEntityTranslation
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private ?int $id = null;

    #[Column(type: 'string')]
    private ?string $extendedTitle = null;

    public function getExtendedTitle(): string
    {
        if (null === $this->extendedTitle) {
            throw new \Exception();
        }

        return $this->extendedTitle;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setExtendedTitle(string $title): void
    {
        $this->extendedTitle = $title;
    }
}
