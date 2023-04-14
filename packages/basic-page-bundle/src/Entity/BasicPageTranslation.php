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
use Gedmo\Mapping\Annotation as Gedmo;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(indexes={
 *     @ORM\Index(columns={"slug"}),
 * })
 */
class BasicPageTranslation implements TranslationInterface
{
    use TranslationTrait;

    /**
     * @var string|null
     *
     * This property is needed to do the Join::WITH on the BasicPageRepository
     *
     * @ORM\Column(type="string", length=5)
     */
    protected $locale;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @Assert\NotNull
     * @Assert\Length(max=255)
     *
     * @ORM\Column(type="string")
     */
    private ?string $title = null;

    /**
     * @Gedmo\Slug(fields={"title"}, unique_base="locale")
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $slug = null;

    /**
     * @Assert\NotNull
     *
     * @ORM\Column(type="text")
     */
    private ?string $content = null;

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }
}
