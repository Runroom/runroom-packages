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

namespace Runroom\RedirectionBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Runroom\RedirectionBundle\Repository\RedirectRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RedirectRepository::class)]
#[DoctrineAssert\UniqueEntity('source')]
class Redirect implements \Stringable
{
    final public const PERMANENT = 301;
    final public const TEMPORAL = 302;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 500, unique: true)]
    #[Assert\NotNull]
    #[Assert\Length(max: 500)]
    #[Assert\Regex('/^\/.*$/')]
    private ?string $source = null;

    #[ORM\Column(type: Types::STRING, length: 500)]
    #[Assert\NotNull]
    #[Assert\Length(max: 500)]
    #[Assert\Regex('/^(\/|https?:\/\/).*$/')]
    #[Assert\NotEqualTo(propertyPath: 'source')]
    private ?string $destination = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\Choice(choices: [self::PERMANENT, self::TEMPORAL])]
    private ?int $httpCode = self::PERMANENT;

    #[ORM\Column(type: Types::BOOLEAN)]
    private ?bool $automatic = false;

    #[ORM\Column(type: Types::BOOLEAN)]
    private ?bool $publish = null;

    public function __toString(): string
    {
        return (string) $this->getSource();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setSource(?string $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setDestination(?string $destination): self
    {
        $this->destination = $destination;

        return $this;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setHttpCode(?int $httpCode): self
    {
        $this->httpCode = $httpCode;

        return $this;
    }

    public function getHttpCode(): ?int
    {
        return $this->httpCode;
    }

    public function setAutomatic(?bool $automatic): self
    {
        $this->automatic = $automatic;

        return $this;
    }

    public function getAutomatic(): ?bool
    {
        return $this->automatic;
    }

    public function setPublish(?bool $publish): self
    {
        $this->publish = $publish;

        return $this;
    }

    public function getPublish(): ?bool
    {
        return $this->publish;
    }
}
