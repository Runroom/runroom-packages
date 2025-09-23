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

namespace Runroom\DoctrineTranslatableBundle\Tests\App\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Runroom\DoctrineTranslatableBundle\Tests\App\Entity\Translatable\AbstractTranslatableEntityTranslation;

/**
 * @psalm-suppress ClassMustBeFinal
 */
#[Entity]
class TranslatableEntityWithCustomInterfaceTranslation extends AbstractTranslatableEntityTranslation
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
