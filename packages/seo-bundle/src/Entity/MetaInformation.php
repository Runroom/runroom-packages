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

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Runroom\SeoBundle\Repository\MetaInformationRepository;
use Sonata\MediaBundle\Model\MediaInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @final
 *
 * @ORM\Entity(repositoryClass=MetaInformationRepository::class)
 */
class MetaInformation implements TranslatableInterface
{
    use ORMBehaviors\Translatable\TranslatableTrait;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     */
    private $route;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $routeName;

    /**
     * @var MediaInterface
     *
     * @Assert\Valid
     */
    private $image;

    public function __toString(): string
    {
        return (string) $this->getRouteName();
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
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

    public function getTitle(string $locale = null): ?string
    {
        return $this->translate($locale, false)->getTitle();
    }

    public function getDescription(string $locale = null): ?string
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
