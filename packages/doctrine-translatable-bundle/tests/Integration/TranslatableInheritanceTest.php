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

namespace Runroom\DoctrineTranslatableBundle\Tests\Integration;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Runroom\DoctrineTranslatableBundle\Tests\App\Entity\Translatable\ExtendedTranslatableEntity;
use Runroom\DoctrineTranslatableBundle\Tests\App\Entity\Translatable\ExtendedTranslatableEntityTranslation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

final class TranslatableInheritanceTest extends KernelTestCase
{
    use ResetDatabase;

    private EntityManagerInterface $entityManager;

    /**
     * @var ObjectRepository<ExtendedTranslatableEntity>
     */
    private ObjectRepository $objectRepository;

    protected function setUp(): void
    {
        $this->entityManager = static::getContainer()->get('doctrine.orm.entity_manager');
        $this->objectRepository = $this->entityManager->getRepository(ExtendedTranslatableEntity::class);
    }

    public function testShouldPersistTranslationsWithInheritance(): void
    {
        $entity = new ExtendedTranslatableEntity();

        /** @var ExtendedTranslatableEntityTranslation $frenchEntity */
        $frenchEntity = $entity->translate('fr');
        $frenchEntity->setTitle('fabuleux');
        $frenchEntity->setExtendedTitle('fabuleux');

        /** @var ExtendedTranslatableEntityTranslation $englishEntity */
        $englishEntity = $entity->translate('en');
        $englishEntity->setTitle('awesome');
        $englishEntity->setExtendedTitle('awesome');

        /** @var ExtendedTranslatableEntityTranslation $russianEntity */
        $russianEntity = $entity->translate('ru');
        $russianEntity->setTitle('удивительный');
        $russianEntity->setExtendedTitle('удивительный');

        $entity->mergeNewTranslations();

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $id = $entity->getId();

        $this->entityManager->clear();

        /** @var ExtendedTranslatableEntity $entity */
        $entity = $this->objectRepository->find($id);

        /** @var ExtendedTranslatableEntityTranslation $frenchEntity */
        $frenchEntity = $entity->translate('fr');
        static::assertSame('fabuleux', $frenchEntity->getTitle());
        static::assertSame('fabuleux', $frenchEntity->getExtendedTitle());

        /** @var ExtendedTranslatableEntityTranslation $englishEntity */
        $englishEntity = $entity->translate('en');
        static::assertSame('awesome', $englishEntity->getTitle());
        static::assertSame('awesome', $englishEntity->getExtendedTitle());

        /** @var ExtendedTranslatableEntityTranslation $russianEntity */
        $russianEntity = $entity->translate('ru');
        static::assertSame('удивительный', $russianEntity->getTitle());
        static::assertSame('удивительный', $russianEntity->getExtendedTitle());
    }
}
