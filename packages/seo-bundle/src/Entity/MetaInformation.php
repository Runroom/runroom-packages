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
use Runroom\SeoBundle\Repository\MetaInformationRepository;
use Sonata\MediaBundle\Model\MediaInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MetaInformationRepository::class)]
class MetaInformation implements TranslatableInterface, \Stringable
{
    use TranslatableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, unique: true)]
    private ?string $route = null;

    #[ORM\Column(type: Types::STRING)]
    private ?string $routeName = null;

    #[Assert\Valid]
    private ?MediaInterface $image = null;

    public function __toString(): string
    {
        return (string) $this->getRouteName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setRoute(?string $route): self
    {
        $this->route = $route;

        return $this;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function setRouteName(?string $routeName): self
    {
        $this->routeName = $routeName;

        return $this;
    }

    public function getRouteName(): ?string
    {
        return $this->routeName;
    }

    public function getTitle(?string $locale = null): ?string
    {
        return $this->translate($locale, false)->getTitle();
    }

    public function getDescription(?string $locale = null): ?string
    {
        return $this->translate($locale, false)->getDescription();
    }

    public function setImage(?MediaInterface $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getImage(): ?MediaInterface
    {
        return $this->image;
    }
}
