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
use Doctrine\ORM\Mapping\MappedSuperclass;
use Runroom\DoctrineTranslatableBundle\Entity\TranslationInterface;
use Runroom\DoctrineTranslatableBundle\Model\TranslationTrait;

#[MappedSuperclass]
abstract class AbstractTranslatableEntityTranslation implements TranslationInterface
{
    use TranslationTrait;

    #[Column(type: 'string')]
    private ?string $title = null;

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
