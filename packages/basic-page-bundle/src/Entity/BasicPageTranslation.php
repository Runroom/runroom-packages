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
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @final
 *
 * @ORM\Entity
 * @ORM\Table(indexes={
 *     @ORM\Index(columns={"slug"}),
 * })
 */
class BasicPageTranslation implements TranslationInterface
{
    use ORMBehaviors\Translatable\TranslationTrait;

    /**
     * @var int
     *
     * @ORM\Column(type="string", length=5)
     */
    protected $locale;

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
     * @Assert\NotNull
     * @Assert\Length(max=255)
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"title"}, unique_base="locale")
     * @ORM\Column(type="string", nullable=true)
     */
    private $slug;

    /**
     * @var string
     *
     * @Assert\NotNull
     * @ORM\Column(type="text")
     */
    private $content;

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
