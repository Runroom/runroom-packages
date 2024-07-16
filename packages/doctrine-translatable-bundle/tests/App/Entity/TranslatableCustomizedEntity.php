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

namespace Runroom\DoctrineTranslatableBundle\Tests\App\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Runroom\DoctrineTranslatableBundle\Entity\TranslatableInterface;
use Runroom\DoctrineTranslatableBundle\Model\TranslatableTrait;
use Runroom\DoctrineTranslatableBundle\Tests\App\Entity\Translation\TranslatableCustomizedEntityTranslation;

/**
 * Used to test translation classes which declare custom translatable classes.
 */
#[Entity]
class TranslatableCustomizedEntity implements TranslatableInterface
{
    use TranslatableTrait;

    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    private ?int $id = null;

    /**
     * @param array<string, mixed> $arguments
     */
    public function __call(string $method, array $arguments): mixed
    {
        return $this->proxyCurrentLocaleTranslation($method, $arguments);
    }

    public static function getTranslationEntityClass(): string
    {
        return TranslatableCustomizedEntityTranslation::class;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
