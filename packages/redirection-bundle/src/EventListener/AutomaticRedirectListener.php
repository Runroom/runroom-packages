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

namespace Runroom\RedirectionBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Runroom\RedirectionBundle\Entity\Redirect;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\Exception\ExceptionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class AutomaticRedirectListener
{
    private const PREVIOUS_VALUE = 0;
    private const NEXT_VALUE = 1;

    /**
     * @param array<class-string, array{ route: string, routeParameters: array<string, string> }> $configuration
     */
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly PropertyAccessorInterface $propertyAccessor,
        private array $configuration,
    ) {}

    public function onFlush(OnFlushEventArgs $args): void
    {
        $entityManager = $args->getObjectManager();
        $unitOfWork = $entityManager->getUnitOfWork();

        foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
            if (null !== ($redirect = $this->createRedirectFromEntityChanges($entityManager, $entity))) {
                $entityManager->persist($redirect);
                $unitOfWork->computeChangeSet($entityManager->getClassMetadata(Redirect::class), $redirect);

                $this->modifyRelatedRedirects($entityManager, $redirect);
                $this->removeLoopRedirects($entityManager, $redirect);
            }
        }
    }

    private function createRedirectFromEntityChanges(EntityManagerInterface $entityManager, object $entity): ?Redirect
    {
        if (isset($this->configuration[$entity::class])) {
            $source = $this->generateUrl($entityManager, $entity);
            $destination = $this->generateUrl($entityManager, $entity, self::NEXT_VALUE);

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

    private function generateUrl(EntityManagerInterface $entityManager, object $entity, int $state = self::PREVIOUS_VALUE): ?string
    {
        $redirectConfiguration = $this->configuration[$entity::class];
        $uow = $entityManager->getUnitOfWork();
        $changeset = $uow->getEntityChangeSet($entity);

        try {
            return $this->urlGenerator->generate(
                $redirectConfiguration['route'],
                array_map(
                    /**
                     * @psalm-return mixed
                     */
                    fn (string $field) => $changeset[$field][$state] ?? $this->propertyAccessor->getValue($entity, $field),
                    $redirectConfiguration['routeParameters']
                )
            );
        } catch (ExceptionInterface) {
            return null;
        }
    }

    private function modifyRelatedRedirects(EntityManagerInterface $entityManager, Redirect $redirect): void
    {
        $repository = $entityManager->getRepository(Redirect::class);
        $metadata = $entityManager->getClassMetadata(Redirect::class);
        $unitOfWork = $entityManager->getUnitOfWork();

        $relatedRedirects = $repository->findBy([
            'destination' => $redirect->getSource(),
            'automatic' => true,
        ]);

        foreach ($relatedRedirects as $relatedRedirect) {
            $relatedRedirect->setDestination($redirect->getDestination());
            $entityManager->persist($relatedRedirect);
            $unitOfWork->computeChangeSet($metadata, $relatedRedirect);
        }
    }

    private function removeLoopRedirects(EntityManagerInterface $entityManager, Redirect $redirect): void
    {
        $repository = $entityManager->getRepository(Redirect::class);
        $metadata = $entityManager->getClassMetadata(Redirect::class);
        $unitOfWork = $entityManager->getUnitOfWork();

        $loopRedirects = $repository->findBy([
            'source' => $redirect->getDestination(),
            'automatic' => true,
        ]);

        foreach ($loopRedirects as $loopRedirect) {
            $entityManager->remove($loopRedirect);
            $unitOfWork->computeChangeSet($metadata, $loopRedirect);
        }
    }
}
