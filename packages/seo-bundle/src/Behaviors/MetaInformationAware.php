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

namespace Runroom\SeoBundle\Behaviors;

use Doctrine\ORM\Mapping as ORM;
use Runroom\SeoBundle\Entity\EntityMetaInformation;

/**
 * Keep annotations and attributes since this class is mean to be used by end user entities.
 */
trait MetaInformationAware
{
    /**
     * @ORM\OneToOne(targetEntity="Runroom\SeoBundle\Entity\EntityMetaInformation", cascade={"all"})
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    #[ORM\OneToOne(targetEntity: EntityMetaInformation::class, cascade: ['all'])]
    #[ORM\JoinColumn(referencedColumnName: 'id')]
    private ?EntityMetaInformation $metaInformation = null;

    public function setMetaInformation(?EntityMetaInformation $metaInformation): self
    {
        $this->metaInformation = $metaInformation;

        return $this;
    }

    public function getMetaInformation(): ?EntityMetaInformation
    {
        return $this->metaInformation;
    }
}
