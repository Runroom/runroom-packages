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

namespace Runroom\CookiesBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Runroom\DoctrineTranslatableBundle\Entity\TranslationInterface;
use Runroom\DoctrineTranslatableBundle\Model\TranslationTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @api
 */
#[ORM\Entity]
class CookiesPageTranslation implements TranslationInterface
{
    use TranslationTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotNull]
    #[Assert\Length(max: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
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
