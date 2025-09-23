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

namespace Runroom\SeoBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Runroom\DoctrineTranslatableBundle\Entity\TranslatableInterface;
use Runroom\DoctrineTranslatableBundle\Model\TranslatableTrait;

/**
 * @api
 */
#[ORM\Entity]
class EntityMetaInformation implements TranslatableInterface, \Stringable
{
    use TranslatableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    public function __toString(): string
    {
        return (string) $this->getTitle();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(?string $locale = null): ?string
    {
        return $this->translate($locale, false)->getTitle();
    }

    public function getDescription(?string $locale = null): ?string
    {
        return $this->translate($locale, false)->getDescription();
    }
}
