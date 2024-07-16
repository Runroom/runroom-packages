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

#[Entity]
class ExtendedTranslatableEntityWithJoinTableInheritance extends TranslatableEntityWithJoinTableInheritance
{
    #[Column(type: 'string')]
    private ?string $untranslatedField = null;

    public function getUntranslatedField(): string
    {
        if (null === $this->untranslatedField) {
            throw new \Exception();
        }

        return $this->untranslatedField;
    }

    public function setUntranslatedField(string $untranslatedField): void
    {
        $this->untranslatedField = $untranslatedField;
    }
}
