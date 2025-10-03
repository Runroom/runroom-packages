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

namespace Runroom\DoctrineTranslatableBundle\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\ObjectManager;
use Runroom\DoctrineTranslatableBundle\Entity\TranslatableInterface;
use Runroom\DoctrineTranslatableBundle\Entity\TranslationInterface;
use Runroom\DoctrineTranslatableBundle\Provider\LocaleProviderInterface;

final class TranslatableEventSubscriber
{
    /**
     * @var string
     */
    public const LOCALE = 'locale';

    private readonly int $translatableFetchMode;

    private readonly int $translationFetchMode;

    public function __construct(
        private readonly LocaleProviderInterface $localeProvider,
        string $translatableFetchMode,
        string $translationFetchMode,
    ) {
        $this->translatableFetchMode = $this->convertFetchString($translatableFetchMode);
        $this->translationFetchMode = $this->convertFetchString($translationFetchMode);
    }

    /**
     * Adds mapping to the translatable and translations.
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $loadClassMetadataEventArgs): void
    {
        $classMetadata = $loadClassMetadataEventArgs->getClassMetadata();
        \assert($classMetadata instanceof ClassMetadata);

        $reflectionClass = $classMetadata->getReflectionClass();

        if ($classMetadata->isMappedSuperclass) {
            return;
        }

        if (is_a($reflectionClass->getName(), TranslatableInterface::class, true)) {
            $this->mapTranslatable($reflectionClass, $classMetadata);
        }

        if (is_a($reflectionClass->getName(), TranslationInterface::class, true)) {
            $this->mapTranslation($reflectionClass, $classMetadata, $loadClassMetadataEventArgs->getObjectManager());
        }
    }

    public function postLoad(PostLoadEventArgs $lifecycleEventArgs): void
    {
        $this->setLocales($lifecycleEventArgs);
    }

    public function prePersist(PrePersistEventArgs $lifecycleEventArgs): void
    {
        $this->setLocales($lifecycleEventArgs);
    }

    /**
     * Convert string FETCH mode to required string.
     */
    private function convertFetchString(string|int $fetchMode): int
    {
        if (\is_int($fetchMode)) {
            return $fetchMode;
        }

        if ('EAGER' === $fetchMode) {
            return ClassMetadata::FETCH_EAGER;
        }

        if ('EXTRA_LAZY' === $fetchMode) {
            return ClassMetadata::FETCH_EXTRA_LAZY;
        }

        return ClassMetadata::FETCH_LAZY;
    }

    /**
     * @param \ReflectionClass<object> $reflectionClass
     * @param ClassMetadata<object>    $classMetadata
     */
    private function mapTranslatable(\ReflectionClass $reflectionClass, ClassMetadata $classMetadata): void
    {
        if ($classMetadata->hasAssociation('translations')) {
            return;
        }

        $classMetadata->mapOneToMany([
            'fieldName' => 'translations',
            'mappedBy' => 'translatable',
            'indexBy' => self::LOCALE,
            'cascade' => ['persist', 'remove'],
            'fetch' => $this->translatableFetchMode,
            'targetEntity' => $reflectionClass
                ->getMethod('getTranslationEntityClass')
                ->invoke(null),
            'orphanRemoval' => true,
        ]);
    }

    /**
     * @param \ReflectionClass<object> $reflectionClass
     * @param ClassMetadata<object>    $classMetadata
     */
    private function mapTranslation(
        \ReflectionClass $reflectionClass,
        ClassMetadata $classMetadata,
        ObjectManager $objectManager,
    ): void {
        if (!$classMetadata->hasAssociation('translatable')) {
            /** @var class-string $targetEntity */
            $targetEntity = $reflectionClass
                ->getMethod('getTranslatableEntityClass')
                ->invoke(null);

            $targetClassMetadata = $objectManager->getClassMetadata($targetEntity);
            \assert($targetClassMetadata instanceof ClassMetadata);

            $singleIdentifierFieldName = $targetClassMetadata->getSingleIdentifierFieldName();

            $classMetadata->mapManyToOne([
                'fieldName' => 'translatable',
                'inversedBy' => 'translations',
                'cascade' => ['persist'],
                'fetch' => $this->translationFetchMode,
                'joinColumns' => [[
                    'name' => 'translatable_id',
                    'referencedColumnName' => $singleIdentifierFieldName,
                    'onDelete' => 'CASCADE',
                ]],
                'targetEntity' => $targetEntity,
            ]);
        }

        $name = $classMetadata->getTableName() . '_unique_translation';
        if (
            !$this->hasUniqueTranslationConstraint($classMetadata, $name)
            && $classMetadata->getName() === $classMetadata->rootEntityName
        ) {
            $classMetadata->table['uniqueConstraints'][$name] = [
                'columns' => ['translatable_id', self::LOCALE],
            ];
        }

        if (!$classMetadata->hasField(self::LOCALE) && !$classMetadata->hasAssociation(self::LOCALE)) {
            $classMetadata->mapField([
                'fieldName' => self::LOCALE,
                'type' => 'string',
                'length' => 5,
            ]);
        }
    }

    /**
     * @param LifecycleEventArgs<EntityManagerInterface> $lifecycleEventArgs
     */
    private function setLocales(LifecycleEventArgs $lifecycleEventArgs): void
    {
        $entity = $lifecycleEventArgs->getObject();
        if (!$entity instanceof TranslatableInterface) {
            return;
        }

        $currentLocale = $this->localeProvider->provideCurrentLocale();
        if (null !== $currentLocale) {
            $entity->setCurrentLocale($currentLocale);
        }

        $fallbackLocale = $this->localeProvider->provideFallbackLocale();
        if (null !== $fallbackLocale) {
            $entity->setDefaultLocale($fallbackLocale);
        }
    }

    /**
     * @param ClassMetadata<object> $classMetadata
     */
    private function hasUniqueTranslationConstraint(ClassMetadata $classMetadata, string $name): bool
    {
        return isset($classMetadata->table['uniqueConstraints'][$name]);
    }
}
