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
use Doctrine\ORM\Events;
use Runroom\RedirectionBundle\Listener\AutomaticRedirectSubscriber;
use Runroom\RedirectionBundle\Repository\RedirectRepository;
use Runroom\RedirectionBundle\Tests\App\Entity\Entity;
use Runroom\RedirectionBundle\Tests\App\Entity\WrongEntity;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

class AutomaticRedirectSubscriberTest extends KernelTestCase
{
    use ResetDatabase;

    /** @var RedirectRepository */
    private $repository;

    /** @var AutomaticRedirectSubscriber */
    private $subscriber;

    /** @var EntityManagerInterface */
    private $entityManager;

    protected function setUp(): void
    {
        parent::bootKernel();

        $this->repository = static::$container->get(RedirectRepository::class);
        $this->subscriber = static::$container->get(AutomaticRedirectSubscriber::class);
        $this->entityManager = static::$container->get(EntityManagerInterface::class);
    }

    /** @test */
    public function itDoesSubscribeToOnFlushEvent(): void
    {
        $events = $this->subscriber->getSubscribedEvents();

        self::assertSame([Events::onFlush], $events);
    }

    /** @test */
    public function itTestAutomaticRedirectCreation(): void
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

        self::assertCount(2, $redirects);

        foreach ($redirects as $redirect) {
            self::assertTrue($redirect->getAutomatic());
        }
    }

    /** @test */
    public function itDoesNotGenerateRedirectsIfThereIsAConfigurationMistake(): void
    {
        $entity = new WrongEntity();
        $entity->setSlug('test');

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $entity->setSlug('another-test');

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $redirects = $this->repository->findBy(['automatic' => true]);

        self::assertCount(0, $redirects);
    }
}
