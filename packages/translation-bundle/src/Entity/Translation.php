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

namespace Runroom\TranslationBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Runroom\DoctrineTranslatableBundle\Entity\TranslatableInterface;
use Runroom\DoctrineTranslatableBundle\Model\TranslatableTrait;
use Runroom\TranslationBundle\Repository\TranslationRepository;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @api
 */
#[ORM\Entity(repositoryClass: TranslationRepository::class)]
class Translation implements TranslatableInterface, \Stringable
{
    use TranslatableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(name: 'translation_key', type: Types::STRING, unique: true)]
    #[Assert\NotNull]
    #[Assert\Length(max: 255)]
    private ?string $key = null;

    public function __toString(): string
    {
        return (string) $this->getKey();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setKey(?string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function getValue(?string $locale = null): ?string
    {
        return $this->translate($locale, false)->getValue();
    }
}
