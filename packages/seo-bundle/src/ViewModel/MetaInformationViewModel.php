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

namespace Runroom\SeoBundle\ViewModel;

use Sonata\MediaBundle\Model\MediaInterface;

final class MetaInformationViewModel
{
    /** @var string */
    private $title;

    /** @var string */
    private $description;

    /** @var MediaInterface */
    private $image;

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setImage(?MediaInterface $image): void
    {
        $this->image = $image;
    }

    public function getImage(): ?MediaInterface
    {
        return $this->image;
    }
}
