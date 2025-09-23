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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Runroom\DoctrineTranslatableBundle\Entity\TranslationInterface;
use Runroom\DoctrineTranslatableBundle\Exception\TranslatableException;

trait TranslatableMethodsTrait
{
    /**
     * @return Collection<string, TranslationInterface>
     */
    public function getTranslations()
    {
        // initialize collection, usually in ctor
        if (null === $this->translations) {
            $this->translations = new ArrayCollection();
        }

        return $this->translations;
    }

    /**
     * @param Collection<string, TranslationInterface> $translations
     *
     * @phpstan-param iterable<TranslationInterface> $translations
     */
    public function setTranslations(iterable $translations): void
    {
        $this->ensureIsIterableOrCollection($translations);

        foreach ($translations as $translation) {
            $this->addTranslation($translation);
        }
    }

    /**
     * @return Collection<string, TranslationInterface>
     */
    public function getNewTranslations(): Collection
    {
        // initialize collection, usually in ctor
        if (null === $this->newTranslations) {
            $this->newTranslations = new ArrayCollection();
        }

        return $this->newTranslations;
    }

    public function addTranslation(TranslationInterface $translation): void
    {
        $this->getTranslations()->set($translation->getLocale(), $translation);

        $translation->setTranslatable($this);
    }

    public function removeTranslation(TranslationInterface $translation): void
    {
        $this->getTranslations()->removeElement($translation);
    }

    /**
     * Returns translation for specific locale (creates new one if doesn't exists). If requested translation doesn't
     * exist, it will first try to fallback default locale If any translation doesn't exist, it will be added to
     * newTranslations collection. In order to persist new translations, call mergeNewTranslations method, before flush.
     *
     * @param string $locale The locale (en, ru, fr) | null If null, will try with current locale
     */
    public function translate(?string $locale = null, bool $fallbackToDefault = true): TranslationInterface
    {
        return $this->doTranslate($locale, $fallbackToDefault);
    }

    /**
     * Merges newly created translations into persisted translations.
     */
    public function mergeNewTranslations(): void
    {
        foreach ($this->getNewTranslations() as $newTranslation) {
            if (!$this->getTranslations()->contains($newTranslation) && !$newTranslation->isEmpty()) {
                $this->addTranslation($newTranslation);
                $this->getNewTranslations()->removeElement($newTranslation);
            }
        }

        foreach ($this->getTranslations() as $translation) {
            if (!$translation->isEmpty()) {
                continue;
            }

            $this->removeTranslation($translation);
        }
    }

    public function setCurrentLocale(string $locale): void
    {
        $this->currentLocale = $locale;
    }

    public function getCurrentLocale(): string
    {
        return $this->currentLocale ?? $this->getDefaultLocale();
    }

    public function setDefaultLocale(string $locale): void
    {
        $this->defaultLocale = $locale;
    }

    public function getDefaultLocale(): string
    {
        return $this->defaultLocale;
    }

    /**
     * @return class-string
     */
    public static function getTranslationEntityClass(): string
    {
        $class = static::class . 'Translation';

        if (!class_exists($class)) {
            throw new TranslatableException(\sprintf('Translation class "%s" not found', $class));
        }

        return $class;
    }

    /**
     * Returns translation for specific locale (creates new one if doesn't exists). If requested translation doesn't
     * exist, it will first try to fallback default locale If any translation doesn't exist, it will be added to
     * newTranslations collection. In order to persist new translations, call mergeNewTranslations method, before flush.
     *
     * @param string $locale The locale (en, ru, fr) | null If null, will try with current locale
     */
    protected function doTranslate(?string $locale = null, bool $fallbackToDefault = true): TranslationInterface
    {
        if (null === $locale) {
            $locale = $this->getCurrentLocale();
        }

        $foundTranslation = $this->findTranslationByLocale($locale);
        if (null !== $foundTranslation && !$foundTranslation->isEmpty()) {
            return $foundTranslation;
        }

        if ($fallbackToDefault) {
            $fallbackTranslation = $this->resolveFallbackTranslation($locale);
            if (null !== $fallbackTranslation) {
                return $fallbackTranslation;
            }
        }

        if (null !== $foundTranslation) {
            return $foundTranslation;
        }

        $translationEntityClass = static::getTranslationEntityClass();

        /** @var TranslationInterface $translation */
        $translation = new $translationEntityClass();
        $translation->setLocale($locale);

        $this->getNewTranslations()->set($translation->getLocale(), $translation);
        $translation->setTranslatable($this);

        return $translation;
    }

    /**
     * An extra feature allows you to proxy translated fields of a translatable entity.
     *
     * @param array<string, mixed> $arguments
     *
     * @return mixed The translated value of the field for current locale
     */
    protected function proxyCurrentLocaleTranslation(string $method, array $arguments = [])
    {
        // allow $entity->name call $entity->getName() in templates
        if (!method_exists(self::getTranslationEntityClass(), $method)) {
            $method = 'get' . ucfirst($method);
        }

        $translation = $this->translate($this->getCurrentLocale());

        $callable = [$translation, $method];

        if (!\is_callable($callable)) {
            throw new TranslatableException(
                \sprintf('Method "%s" is not callable in translation entity "%s"', $method, $translation::class)
            );
        }

        return \call_user_func_array($callable, $arguments);
    }

    /**
     * Finds specific translation in collection by its locale.
     */
    protected function findTranslationByLocale(string $locale, bool $withNewTranslations = true): ?TranslationInterface
    {
        $translation = $this->getTranslations()->get($locale);

        if (null !== $translation) {
            return $translation;
        }

        if ($withNewTranslations) {
            return $this->getNewTranslations()->get($locale);
        }

        return null;
    }

    protected function computeFallbackLocale(string $locale): ?string
    {
        $partialLocale = strrchr($locale, '_');

        if (false !== $partialLocale) {
            return substr($locale, 0, -\strlen($partialLocale));
        }

        return null;
    }

    /**
     * @param Collection|mixed $translations
     */
    private function ensureIsIterableOrCollection($translations): void
    {
        if ($translations instanceof Collection) {
            return;
        }

        if (is_iterable($translations)) {
            return;
        }

        throw new TranslatableException(
            \sprintf('$translations parameter must be iterable or %s', Collection::class)
        );
    }

    private function resolveFallbackTranslation(string $locale): ?TranslationInterface
    {
        $fallbackLocale = $this->computeFallbackLocale($locale);

        if (null !== $fallbackLocale) {
            $translation = $this->findTranslationByLocale($fallbackLocale);
            if (null !== $translation && !$translation->isEmpty()) {
                return $translation;
            }
        }

        return $this->findTranslationByLocale($this->getDefaultLocale(), false);
    }
}
