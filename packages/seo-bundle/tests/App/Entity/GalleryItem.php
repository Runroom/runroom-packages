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

namespace Runroom\SeoBundle\Tests\App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sonata\MediaBundle\Entity\BaseGalleryItem;

/* @todo: Keep only the if part when dropping support for sonata-project/media-bundle 3 */
if (!class_exists(BaseGalleryItem::class)) {
    class_alias('Sonata\MediaBundle\Entity\BaseGalleryHasMedia', BaseGalleryItem::class);
}

/** @ORM\Entity */
class GalleryItem extends BaseGalleryItem
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
