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

trait MetaInformationAware
{
    /**
     * @var EntityMetaInformation
     *
     * @ORM\OneToOne(targetEntity="Runroom\SeoBundle\Entity\EntityMetaInformation", cascade={"all"})
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $metaInformation;

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
