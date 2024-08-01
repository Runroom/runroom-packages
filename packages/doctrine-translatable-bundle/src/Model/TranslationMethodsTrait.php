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
use Runroom\DoctrineTranslatableBundle\Exception\TranslatableException;

trait TranslationMethodsTrait
{
    public static function getTranslatableEntityClass(): string
    {
        // By default, the translatable class has the same name but without the "Translation" suffix
        return substr(static::class, 0, -11);
    }

    /**
     * Sets entity, that this translation should be mapped to.
     */
    public function setTranslatable(TranslatableInterface $translatable): void
    {
        $this->translatable = $translatable;
    }

    /**
     * Returns entity, that this translation is mapped to.
     */
    public function getTranslatable(): TranslatableInterface
    {
        if (null === $this->translatable) {
            throw new TranslatableException('Translatable is not set.');
        }

        return $this->translatable;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getLocale(): string
    {
        if (null === $this->locale) {
            throw new TranslatableException('Locale is not set.');
        }

        return $this->locale;
    }

    public function isEmpty(): bool
    {
        foreach (get_object_vars($this) as $var => $value) {
            if (\in_array($var, ['id', 'translatable', 'locale'], true)) {
                continue;
            }

            if (!$this->isEmptyValue($value)) {
                return false;
            }
        }

        return true;
    }

    private function isEmptyValue(mixed $value): bool
    {
        if (\is_string($value)) {
            return '' === trim($value);
        }

        return null === $value;
    }
}
