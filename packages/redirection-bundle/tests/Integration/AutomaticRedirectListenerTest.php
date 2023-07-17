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

namespace Runroom\RedirectionBundle\Tests\Integration;

use Doctrine\ORM\EntityManagerInterface;
use Runroom\RedirectionBundle\Repository\RedirectRepository;
use Runroom\RedirectionBundle\Tests\App\Entity\Entity;
use Runroom\RedirectionBundle\Tests\App\Entity\WrongEntity;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

final class AutomaticRedirectListenerTest extends KernelTestCase
{
    use ResetDatabase;

    private RedirectRepository $repository;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->repository = static::getContainer()->get(RedirectRepository::class);
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
    }

    public function testItTestAutomaticRedirectCreation(): void
    {
        $entity = new Entity();
        $entity->setTitle('Test');
        $entity->setSlug('test');

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $entity->setSlug('another-test');

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $entity->setSlug('another-test-again');

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $entity->setSlug('test');

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $entity->setTitle('Another Test');

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $redirects = $this->repository->findBy(['destination' => '/entity/test']);

        static::assertCount(2, $redirects);

        foreach ($redirects as $redirect) {
            static::assertTrue($redirect->getAutomatic());
        }
    }

    public function testItDoesNotGenerateRedirectsIfThereIsAConfigurationMistake(): void
    {
        $entity = new WrongEntity();
        $entity->setSlug('test');

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $entity->setSlug('another-test');

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $redirects = $this->repository->findBy(['automatic' => true]);

        static::assertCount(0, $redirects);
    }
}
