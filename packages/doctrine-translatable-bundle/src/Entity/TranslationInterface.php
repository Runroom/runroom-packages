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

namespace Runroom\DoctrineTranslatableBundle\Entity;

interface TranslationInterface
{
    public static function getTranslatableEntityClass(): string;

    public function setTranslatable(TranslatableInterface $translatable): void;

    public function getTranslatable(): TranslatableInterface;

    public function setLocale(string $locale): void;

    public function getLocale(): string;

    public function isEmpty(): bool;
}
