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

namespace Runroom\DoctrineTranslatableBundle\Model;

use Runroom\DoctrineTranslatableBundle\Entity\TranslatableInterface;

trait TranslationPropertiesTrait
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * Will be mapped to translatable entity by TranslatableSubscriber.
     *
     * @var TranslatableInterface
     */
    protected $translatable;
}
