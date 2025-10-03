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

namespace Runroom\DoctrineTranslatableBundle\Tests\ORM\Translatable;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectRepository;
use Runroom\DoctrineTranslatableBundle\Entity\TranslatableInterface;
use Runroom\DoctrineTranslatableBundle\Entity\TranslationInterface;
use Runroom\DoctrineTranslatableBundle\Tests\App\Entity\TranslatableCustomIdentifierEntity;
use Runroom\DoctrineTranslatableBundle\Tests\App\Entity\TranslatableCustomizedEntity;
use Runroom\DoctrineTranslatableBundle\Tests\App\Entity\TranslatableEntity;
use Runroom\DoctrineTranslatableBundle\Tests\App\Entity\TranslatableEntityTranslation;
use Runroom\DoctrineTranslatableBundle\Tests\App\Entity\TranslatableEntityWithCustomInterface;
use Runroom\DoctrineTranslatableBundle\Tests\App\Entity\Translation\TranslatableCustomizedEntityTranslation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

final class TranslatableTest extends KernelTestCase
{
    use ResetDatabase;

    private EntityManagerInterface $entityManager;

    /**
     * @var ObjectRepository<TranslatableEntity>
     */
    private ObjectRepository $translatableRepository;

    protected function setUp(): void
    {
        $this->entityManager = static::getContainer()->get('doctrine.orm.entity_manager');
        $this->translatableRepository = $this->entityManager->getRepository(TranslatableEntity::class);
    }

    public function testShouldPersistTranslations(): void
    {
        $translatableEntity = new TranslatableEntity();
        $translatableEntity->translate('fr')
            ->setTitle('fabuleux');
        $translatableEntity->translate('en')
            ->setTitle('awesome');
        $translatableEntity->translate('ru')
            ->setTitle('удивительный');
        $translatableEntity->mergeNewTranslations();

        $this->entityManager->persist($translatableEntity);
        $this->entityManager->flush();

        $id = $translatableEntity->getId();
        $this->entityManager->clear();

        /** @var TranslatableEntity $translatableEntity */
        $translatableEntity = $this->translatableRepository->find($id);

        static::assertSame('fabuleux', $translatableEntity->translate('fr')->getTitle());
        static::assertSame('awesome', $translatableEntity->translate('en')->getTitle());
        static::assertSame('удивительный', $translatableEntity->translate('ru')->getTitle());
    }

    public function testShouldPersistWithCustomIdentifier(): void
    {
        $translatableEntity = new TranslatableCustomIdentifierEntity();
        $translatableEntity->translate('en')
            ->setTitle('awesome');
        $translatableEntity->mergeNewTranslations();

        $this->entityManager->persist($translatableEntity);
        $this->entityManager->flush();

        $idColumn = $translatableEntity->getIdColumn();
        $this->entityManager->clear();

        $translatableEntity = $this->entityManager->getRepository(TranslatableCustomIdentifierEntity::class)->find(
            $idColumn
        );

        static::assertSame('awesome', $translatableEntity?->translate('en')?->getTitle());
    }

    public function testShouldFallbackCountryLocaleToLanguageOnlyTranslation(): void
    {
        $translatableEntity = new TranslatableEntity();
        $translatableEntity->translate('en', false)
            ->setTitle('plastic bag');
        $translatableEntity->translate('fr', false)
            ->setTitle('sac plastique');
        $translatableEntity->translate('fr_CH', false)
            ->setTitle('cornet');
        $translatableEntity->mergeNewTranslations();

        $this->entityManager->persist($translatableEntity);
        $this->entityManager->flush();

        $id = $translatableEntity->getId();
        $this->entityManager->clear();

        $entity = $this->translatableRepository->find($id);
        static::assertInstanceOf(TranslatableEntity::class, $entity);

        /* @var TranslatableEntity $entity */
        static::assertSame('plastic bag', $entity->translate('de')->getTitle());
        static::assertSame('sac plastique', $entity->translate('fr_FR')->getTitle());
        static::assertSame('cornet', $entity->translate('fr_CH')->getTitle());
    }

    public function testShouldFallbackToDefaultLocaleIfNoCountryLocaleTranslation(): void
    {
        $translatableEntity = new TranslatableEntity();
        $translatableEntity->translate('en', false)
            ->setTitle('plastic bag');
        $translatableEntity->translate('fr_CH', false)
            ->setTitle('cornet');
        $translatableEntity->mergeNewTranslations();

        $this->entityManager->persist($translatableEntity);
        $this->entityManager->flush();

        $id = $translatableEntity->getId();
        $this->entityManager->clear();

        /** @var TranslatableEntity $translatableEntity */
        $translatableEntity = $this->translatableRepository->find($id);

        static::assertSame('plastic bag', $translatableEntity->translate('de')->getTitle());
        static::assertSame('plastic bag', $translatableEntity->translate('fr_FR')->getTitle());
        static::assertSame('cornet', $translatableEntity->translate('fr_CH')->getTitle());
    }

    public function testShouldUpdateAndAddNewTranslations(): void
    {
        $translatableEntity = new TranslatableEntity();
        $translatableEntity->translate('en')
            ->setTitle('awesome');
        $translatableEntity->translate('ru')
            ->setTitle('удивительный');
        $translatableEntity->mergeNewTranslations();

        $this->entityManager->persist($translatableEntity);
        $this->entityManager->flush();

        $id = $translatableEntity->getId();
        $this->entityManager->clear();

        /** @var TranslatableEntity $translatableEntity */
        $translatableEntity = $this->translatableRepository->find($id);

        static::assertSame('awesome', $translatableEntity->translate('en')->getTitle());
        static::assertSame('удивительный', $translatableEntity->translate('ru')->getTitle());

        $translatableEntity->translate('en')
            ->setTitle('great');
        $translatableEntity->translate('fr', false)
            ->setTitle('fabuleux');
        $translatableEntity->mergeNewTranslations();

        $this->entityManager->persist($translatableEntity);
        $this->entityManager->flush();
        $this->entityManager->clear();

        /** @var TranslatableEntity $translatableEntity */
        $translatableEntity = $this->translatableRepository->find($id);

        static::assertSame('great', $translatableEntity->translate('en')->getTitle());
        static::assertSame('fabuleux', $translatableEntity->translate('fr')->getTitle());
        static::assertSame('удивительный', $translatableEntity->translate('ru')->getTitle());
    }

    public function testTranslateMethodShouldAlwaysReturnTranslationObject(): void
    {
        $translatableEntity = new TranslatableEntity();

        static::assertInstanceOf(TranslatableEntityTranslation::class, $translatableEntity->translate('fr'));
    }

    public function testSubscriberShouldConfigureEntityWithCurrentLocale(): void
    {
        $translatableEntity = new TranslatableEntity();
        // magic method
        $translatableEntity->setTitle('test');

        $translatableEntity->mergeNewTranslations();

        $this->entityManager->persist($translatableEntity);

        $this->entityManager->flush();

        $id = $translatableEntity->getId();
        $this->entityManager->clear();

        /** @var TranslatableEntity $translatableEntity */
        $translatableEntity = $this->translatableRepository->find($id);

        static::assertSame('en', $translatableEntity->getCurrentLocale());
        static::assertSame('test', $translatableEntity->getTitle());
        static::assertSame('test', $translatableEntity->translate($translatableEntity->getCurrentLocale())->getTitle());
    }

    public function testSubscriberShouldConfigureEntityWithDefaultLocale(): void
    {
        $translatableEntity = new TranslatableEntity();
        // magic method
        $translatableEntity->setTitle('test');
        $translatableEntity->mergeNewTranslations();

        $this->entityManager->persist($translatableEntity);

        $this->entityManager->flush();

        $id = $translatableEntity->getId();
        $this->entityManager->clear();

        /** @var TranslatableEntity $translatableEntity */
        $translatableEntity = $this->translatableRepository->find($id);

        static::assertSame('en', $translatableEntity->getDefaultLocale());
        // magic method
        static::assertSame('test', $translatableEntity->getTitle());

        static::assertSame('test', $translatableEntity->translate($translatableEntity->getDefaultLocale())->getTitle());
        static::assertSame('test', $translatableEntity->translate('fr')->getTitle());
    }

    public function testShouldHaveOneToManyRelation(): void
    {
        $this->assertTranslationsOneToManyMapped(TranslatableEntity::class, TranslatableEntityTranslation::class);
    }

    public function testShouldHaveOneToManyRelationWhenTranslationClassNameIsCustom(): void
    {
        $this->assertTranslationsOneToManyMapped(
            TranslatableCustomizedEntity::class,
            TranslatableCustomizedEntityTranslation::class
        );
    }

    public function testShouldCreateOnlyOneTimeTheSameTranslation(): void
    {
        $translatableEntity = new TranslatableEntity();
        $translatableEntityTranslation = $translatableEntity->translate('fr');
        $translatableEntityTranslation->setTitle('fabuleux');
        $translatableEntity->translate('fr')
            ->setTitle('fabuleux2');
        $translatableEntity->translate('fr')
            ->setTitle('fabuleux3');

        static::assertSame('fabuleux3', $translatableEntity->translate('fr')->getTitle());

        $givenObjectHash = spl_object_hash($translatableEntity->translate('fr'));
        $translationObjectHash = spl_object_hash($translatableEntityTranslation);
        static::assertSame($givenObjectHash, $translationObjectHash);
    }

    public function testShouldRemoveTranslation(): void
    {
        $translatableEntity = new TranslatableEntity();
        $translatableEntity->translate('en')
            ->setTitle('Hello');
        $translatableEntity->translate('nl')
            ->setTitle('Hallo');
        $translatableEntity->mergeNewTranslations();
        $this->entityManager->persist($translatableEntity);
        $this->entityManager->flush();

        $translatableEntityTranslation = $translatableEntity->translate('nl');
        $translatableEntity->removeTranslation($translatableEntityTranslation);
        $this->entityManager->flush();

        $this->entityManager->refresh($translatableEntity);
        static::assertNotSame('Hallo', $translatableEntity->translate('nl')->getTitle());
    }

    public function testSetTranslations(): void
    {
        $translatableEntity = new TranslatableEntity();
        $translatableEntityTranslation = $translatableEntity->translate('en');

        $translatableEntity->setTranslations([$translatableEntityTranslation]);

        static::assertCount(1, $translatableEntity->getTranslations());
    }

    public function testShouldNotPersistNewEmptyTranslations(): void
    {
        $translatableEntity = new TranslatableEntity();
        $translatableEntity->translate('fr')
            ->setTitle('fabuleux');
        $translatableEntity->translate('en')
            ->setTitle('');
        $translatableEntity->translate('ru')
            ->setTitle('удивительный');

        $translatableEntity->mergeNewTranslations();

        $this->entityManager->persist($translatableEntity);
        $this->entityManager->flush();

        $id = $translatableEntity->getId();
        $this->entityManager->clear();

        $entity = $this->translatableRepository->find($id);
        static::assertIsObject($entity);
        static::assertInstanceOf(TranslatableEntity::class, $entity);

        /* @var TranslatableEntity $entity */
        static::assertSame('fabuleux', $entity->translate('fr')->getTitle());

        // empty English translation
        static::assertNull($entity->translate('en')->getTitle());

        static::assertSame('удивительный', $entity->translate('ru')->getTitle());
    }

    public function testShouldRemoveTranslationsWhichBecomeEmpty(): void
    {
        $translatableEntity = new TranslatableEntity();
        $translatableEntity->translate('fr')
            ->setTitle('fabuleux');
        $translatableEntity->translate('en')
            ->setTitle('awesome');
        $translatableEntity->translate('ru')
            ->setTitle('удивительный');

        $translatableEntity->mergeNewTranslations();

        $this->entityManager->persist($translatableEntity);
        $this->entityManager->flush();

        $translatableEntity->translate('en')
            ->setTitle('');
        $translatableEntity->mergeNewTranslations();

        $this->entityManager->persist($translatableEntity);
        $this->entityManager->flush();

        $id = $translatableEntity->getId();
        $this->entityManager->clear();

        $translatableEntity = $this->translatableRepository->find($id);

        static::assertIsObject($translatableEntity);
        static::assertInstanceOf(TranslatableEntity::class, $translatableEntity);

        /* @var TranslatableEntity $translatableEntity */
        static::assertSame('fabuleux', $translatableEntity->translate('fr')->getTitle());
        static::assertNull($translatableEntity->translate('en')->getTitle());
        static::assertSame('удивительный', $translatableEntity->translate('ru')->getTitle());
    }

    public function testPhpStanExtensionOnInterfaces(): void
    {
        /** @var TranslationInterface $translatableEntityTranslation */
        $translatableEntityTranslation = new TranslatableEntityTranslation();
        $translatableEntityTranslation->setLocale('fr');

        /** @var TranslatableInterface $translatableEntity */
        $translatableEntity = new TranslatableEntity();
        $translatableEntity->addTranslation($translatableEntityTranslation);

        static::assertSame($translatableEntity, $translatableEntityTranslation->getTranslatable());
        static::assertSame($translatableEntityTranslation, $translatableEntity->getTranslations()->get('fr'));
    }

    public function testTranslationIsNotEmptyWithZeroAsValue(): void
    {
        $translatableEntity = new TranslatableEntity();
        $translatableEntity->translate('fr')
            ->setTitle('0');
        $translatableEntity->translate('en')
            ->setTitle('0');

        $translatableEntity->mergeNewTranslations();

        $this->entityManager->persist($translatableEntity);
        $this->entityManager->flush();

        $id = $translatableEntity->getId();
        $this->entityManager->clear();

        /** @var TranslatableEntity $translatableEntity */
        $translatableEntity = $this->translatableRepository->find($id);

        static::assertFalse($translatableEntity->translate('fr')->isEmpty());
        static::assertFalse($translatableEntity->translate('en')->isEmpty());
        static::assertSame('0', $translatableEntity->translate('fr')->getTitle());
        static::assertSame('0', $translatableEntity->translate('en')->getTitle());
    }

    public function testCustomInterface(): void
    {
        $translatableEntityWithCustom = new TranslatableEntityWithCustomInterface();
        $translatableEntityWithCustom->translate('en')
            ->setTitle('awesome');
        $translatableEntityWithCustom->mergeNewTranslations();

        static::assertSame('awesome', $translatableEntityWithCustom->translate('en')->getTitle());
    }

    /**
     * @param class-string $translatableClass
     * @param class-string $translationClass
     *                                        Asserts that the one to many relationship between translatable and translations is mapped correctly
     */
    private function assertTranslationsOneToManyMapped(string $translatableClass, string $translationClass): void
    {
        $translationClassMetadata = $this->entityManager->getClassMetadata($translationClass);
        static::assertSame($translatableClass, $translationClassMetadata->getAssociationTargetClass('translatable'));

        $translatableClassMetadata = $this->entityManager->getClassMetadata($translatableClass);
        static::assertSame($translationClass, $translatableClassMetadata->getAssociationTargetClass('translations'));

        static::assertTrue($translatableClassMetadata->isAssociationInverseSide('translations'));

        static::assertSame(
            ClassMetadata::ONE_TO_MANY,
            $translatableClassMetadata->getAssociationMapping('translations')['type']
        );
    }
}
