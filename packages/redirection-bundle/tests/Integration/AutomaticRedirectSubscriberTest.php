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

use Doctrine\ORM\Events;
use Runroom\RedirectionBundle\Listener\AutomaticRedirectSubscriber;
use Runroom\RedirectionBundle\Repository\RedirectRepository;
use Runroom\RedirectionBundle\Tests\App\Entity\Entity;
use Runroom\RedirectionBundle\Tests\App\Entity\WrongEntity;
use Runroom\Testing\TestCase\DoctrineTestCase;

class AutomaticRedirectSubscriberTest extends DoctrineTestCase
{
    /** @var RedirectRepository */
    private $repository;

    /** @var AutomaticRedirectSubscriber */
    private $subscriber;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = static::$container->get(RedirectRepository::class);
        $this->subscriber = static::$container->get(AutomaticRedirectSubscriber::class);
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

        static::$entityManager->persist($entity);
        static::$entityManager->flush();

        $entity->setSlug('another-test');

        static::$entityManager->persist($entity);
        static::$entityManager->flush();

        $entity->setSlug('another-test-again');

        static::$entityManager->persist($entity);
        static::$entityManager->flush();

        $entity->setSlug('test');

        static::$entityManager->persist($entity);
        static::$entityManager->flush();

        $entity->setTitle('Another Test');

        static::$entityManager->persist($entity);
        static::$entityManager->flush();

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

        static::$entityManager->persist($entity);
        static::$entityManager->flush();

        $entity->setSlug('another-test');

        static::$entityManager->persist($entity);
        static::$entityManager->flush();

        $redirects = $this->repository->findBy(['automatic' => true]);

        self::assertCount(0, $redirects);
    }

    protected function getDataFixtures(): array
    {
        return ['redirects.yaml'];
    }
}
