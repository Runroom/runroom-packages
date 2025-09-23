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

namespace Runroom\CkeditorSonataMediaBundle\Tests\App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Sonata\MediaBundle\Entity\BaseGallery;

/**
 * @phpstan-extends BaseGallery<GalleryItem>
 *
 * @psalm-suppress ClassMustBeFinal
 */
#[ORM\Entity]
class Gallery extends BaseGallery
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
