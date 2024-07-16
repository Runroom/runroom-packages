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
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\InheritanceType;
use Runroom\DoctrineTranslatableBundle\Entity\TranslationInterface;
use Runroom\DoctrineTranslatableBundle\Model\TranslationTrait;

#[Entity]
#[InheritanceType(value: 'JOINED')]
#[DiscriminatorColumn(name: 'handle', type: 'string')]
class TranslatableEntityWithJoinTableInheritanceTranslation implements TranslationInterface
{
    use TranslationTrait;

    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private ?int $id = null;

    #[Column(type: 'string')]
    private ?string $title = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        if (null === $this->title) {
            throw new \Exception();
        }

        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }
}
