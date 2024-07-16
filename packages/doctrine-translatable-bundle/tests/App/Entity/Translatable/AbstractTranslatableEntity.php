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

use Doctrine\ORM\Mapping\MappedSuperclass;
use Runroom\DoctrineTranslatableBundle\Entity\TranslatableInterface;
use Runroom\DoctrineTranslatableBundle\Model\TranslatableTrait;

#[MappedSuperclass]
abstract class AbstractTranslatableEntity implements TranslatableInterface
{
    use TranslatableTrait;

    /**
     * @param array<string, mixed> $arguments
     */
    public function __call(string $method, array $arguments): mixed
    {
        return $this->proxyCurrentLocaleTranslation($method, $arguments);
    }
}
