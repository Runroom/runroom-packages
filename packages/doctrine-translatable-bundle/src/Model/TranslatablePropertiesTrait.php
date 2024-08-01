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

use Doctrine\Common\Collections\Collection;
use Runroom\DoctrineTranslatableBundle\Entity\TranslationInterface;

trait TranslatablePropertiesTrait
{
    /**
     * @var Collection<string, TranslationInterface>|null
     */
    protected $translations;

    /**
     * @see mergeNewTranslations
     *
     * @var Collection<string, TranslationInterface>|null
     */
    protected $newTranslations;

    /**
     * currentLocale is a non persisted field configured during postLoad event.
     *
     * @var string|null
     */
    protected $currentLocale;

    /**
     * @var string
     */
    protected $defaultLocale = 'en';
}
