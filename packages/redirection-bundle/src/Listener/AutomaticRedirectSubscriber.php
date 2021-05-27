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

namespace Runroom\RedirectionBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Runroom\RedirectionBundle\Entity\Redirect;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\Exception\ExceptionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class AutomaticRedirectSubscriber implements EventSubscriber
{
    private const PREVIOUS_VALUE = 0;
    private const NEXT_VALUE = 1;

    private UrlGeneratorInterface $urlGenerator;
    private PropertyAccessorInterface $propertyAccessor;
    private EntityManagerInterface $entityManager;

    /** @var array<class-string, array{ route: string, routeParameters: array<string, string> }> */
    private array $configuration = [];

    /** @param array<class-string, array{ route: string, routeParameters: array<string, string> }> $configuration */
    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        PropertyAccessorInterface $propertyAccessor,
        array $configuration
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->propertyAccessor = $propertyAccessor;
        $this->configuration = $configuration;
    }

    public function getSubscribedEvents(): array
    {
        return [Events::onFlush];
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        $this->entityManager = $args->getEntityManager();
        $unitOfWork = $this->entityManager->getUnitOfWork();

        foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
            if (null !== ($redirect = $this->createRedirectFromEntityChanges($entity))) {
                $this->entityManager->persist($redirect);
                $unitOfWork->computeChangeSet($this->entityManager->getClassMetadata(Redirect::class), $redirect);

                $this->modifyRelatedRedirects($redirect);
                $this->removeLoopRedirects($redirect);
            }
        }
    }

    private function createRedirectFromEntityChanges(object $entity): ?Redirect
    {
        if (isset($this->configuration[\get_class($entity)])) {
            $source = $this->generateUrl($entity);
            $destination = $this->generateUrl($entity, self::NEXT_VALUE);

            if ($source !== $destination) {
                $redirect = new Redirect();
                $redirect->setSource($source);
                $redirect->setDestination($destination);
                $redirect->setAutomatic(true);
                $redirect->setPublish(true);

                return $redirect;
            }
        }

        return null;
    }

    private function generateUrl(object $entity, int $state = self::PREVIOUS_VALUE): ?string
    {
        $redirectConfiguration = $this->configuration[\get_class($entity)];
        $uow = $this->entityManager->getUnitOfWork();
        $changeset = $uow->getEntityChangeSet($entity);

        try {
            return $this->urlGenerator->generate(
                $redirectConfiguration['route'],
                array_map(function ($field) use ($entity, $changeset, $state) {
                    return $changeset[$field][$state] ?? $this->propertyAccessor->getValue($entity, $field);
                }, $redirectConfiguration['routeParameters'])
            );
        } catch (ExceptionInterface $exception) {
            return null;
        }
    }

    private function modifyRelatedRedirects(Redirect $redirect): void
    {
        $repository = $this->entityManager->getRepository(Redirect::class);
        $metadata = $this->entityManager->getClassMetadata(Redirect::class);
        $unitOfWork = $this->entityManager->getUnitOfWork();

        $relatedRedirects = $repository->findBy([
            'destination' => $redirect->getSource(),
            'automatic' => true,
        ]);

        foreach ($relatedRedirects as $relatedRedirect) {
            $relatedRedirect->setDestination($redirect->getDestination());
            $this->entityManager->persist($relatedRedirect);
            $unitOfWork->computeChangeSet($metadata, $relatedRedirect);
        }
    }

    private function removeLoopRedirects(Redirect $redirect): void
    {
        $repository = $this->entityManager->getRepository(Redirect::class);
        $metadata = $this->entityManager->getClassMetadata(Redirect::class);
        $unitOfWork = $this->entityManager->getUnitOfWork();

        $loopRedirects = $repository->findBy([
            'source' => $redirect->getDestination(),
            'automatic' => true,
        ]);

        foreach ($loopRedirects as $loopRedirect) {
            $this->entityManager->remove($loopRedirect);
            $unitOfWork->computeChangeSet($metadata, $loopRedirect);
        }
    }
}
