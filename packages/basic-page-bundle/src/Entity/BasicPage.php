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

namespace Runroom\BasicPageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use Runroom\BasicPageBundle\Repository\BasicPageRepository;
use Runroom\SeoBundle\Behaviors\MetaInformationAware;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=BasicPageRepository::class)
 *
 * @ORM\Table(indexes={
 *
 *     @ORM\Index(columns={"publish"}),
 * })
 */
class BasicPage implements TranslatableInterface
{
    use MetaInformationAware;
    use TranslatableTrait;

    public const LOCATION_NONE = 'none';
    public const LOCATION_FOOTER = 'footer';

    /**
     * @ORM\Id
     *
     * @ORM\GeneratedValue
     *
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @Assert\Choice(choices = {
     *     BasicPage::LOCATION_NONE,
     *     BasicPage::LOCATION_FOOTER,
     * })
     *
     * @ORM\Column(type="string")
     */
    private ?string $location = self::LOCATION_NONE;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $publish = null;

    public function __toString(): string
    {
        return (string) $this->getTitle();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
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

    public function getTitle(string $locale = null): ?string
    {
        return $this->translate($locale, false)->getTitle();
    }

    public function getSlug(string $locale = null): ?string
    {
        return $this->translate($locale, false)->getSlug();
    }

    public function getContent(string $locale = null): ?string
    {
        return $this->translate($locale, false)->getContent();
    }
}
