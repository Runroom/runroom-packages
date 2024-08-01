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
use Doctrine\Persistence\ObjectRepository;
use Runroom\DoctrineTranslatableBundle\Tests\App\Entity\Translatable\ExtendedTranslatableEntityWithJoinTableInheritance;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

final class TranslatableJoinTableInheritanceTest extends KernelTestCase
{
    use ResetDatabase;

    private EntityManagerInterface $entityManager;

    /**
     * @var ObjectRepository<ExtendedTranslatableEntityWithJoinTableInheritance>
     */
    private ObjectRepository $objectRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = static::getContainer()->get('doctrine.orm.entity_manager');
        $this->objectRepository = $this->entityManager->getRepository(
            ExtendedTranslatableEntityWithJoinTableInheritance::class
        );
    }

    public function testShouldPersistTranslationsWithJoinTableInheritance(): void
    {
        $entity = new ExtendedTranslatableEntityWithJoinTableInheritance();
        $entity->setUntranslatedField('untranslated');
        $entity->translate('fr')
            ->setTitle('fabuleux');
        $entity->translate('en')
            ->setTitle('awesome');
        $entity->translate('ru')
            ->setTitle('удивительный');

        $entity->mergeNewTranslations();

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $id = $entity->getId();
        $this->entityManager->clear();

        /** @var ExtendedTranslatableEntityWithJoinTableInheritance $entity */
        $entity = $this->objectRepository->find($id);
        static::assertSame('untranslated', $entity->getUntranslatedField());
        static::assertSame('fabuleux', $entity->translate('fr')->getTitle());
        static::assertSame('awesome', $entity->translate('en')->getTitle());
        static::assertSame('удивительный', $entity->translate('ru')->getTitle());
    }
}
