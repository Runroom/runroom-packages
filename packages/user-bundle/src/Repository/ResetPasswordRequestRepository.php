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

namespace Runroom\UserBundle\Repository;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Runroom\UserBundle\Entity\ResetPasswordRequest;
use Runroom\UserBundle\Model\UserInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Persistence\ResetPasswordRequestRepositoryInterface;

final class ResetPasswordRequestRepository implements ResetPasswordRequestRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    /** @phpstan-var class-string<ResetPasswordRequestInterface> */
    private string $class;

    /** @phpstan-param class-string<ResetPasswordRequestInterface> $class */
    public function __construct(EntityManagerInterface $entityManager, string $class)
    {
        $this->entityManager = $entityManager;
        $this->class = $class;
    }

    public function createResetPasswordRequest(object $user, \DateTimeInterface $expiresAt, string $selector, string $hashedToken): ResetPasswordRequestInterface
    {
        \assert($user instanceof UserInterface);

        return new ResetPasswordRequest($user, $expiresAt, $selector, $hashedToken);
    }

    public function getUserIdentifier(object $user): string
    {
        return (string) $this->entityManager->getUnitOfWork()->getSingleIdentifierValue($user);
    }

    public function persistResetPasswordRequest(ResetPasswordRequestInterface $resetPasswordRequest): void
    {
        $this->entityManager->persist($resetPasswordRequest);
        $this->entityManager->flush();
    }

    public function findResetPasswordRequest(string $selector): ?ResetPasswordRequestInterface
    {
        return $this->getRepository()->findOneBy(['selector' => $selector]);
    }

    public function getMostRecentNonExpiredRequestDate(object $user): ?\DateTimeInterface
    {
        \assert($user instanceof UserInterface);

        $resetPasswordRequest = $this->getRepository()->createQueryBuilder('t')
            ->where('t.user = :user')
            ->setParameter('user', $user->getId())
            ->orderBy('t.requestedAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (null !== $resetPasswordRequest && !$resetPasswordRequest->isExpired()) {
            return $resetPasswordRequest->getRequestedAt();
        }

        return null;
    }

    public function removeResetPasswordRequest(ResetPasswordRequestInterface $resetPasswordRequest): void
    {
        $user = $resetPasswordRequest->getUser();

        \assert($user instanceof UserInterface);

        $this->getRepository()->createQueryBuilder('t')
            ->delete()
            ->where('t.user = :user')
            ->setParameter('user', $user->getId())
            ->getQuery()
            ->execute();
    }

    public function removeExpiredResetPasswordRequests(): int
    {
        $query = $this->getRepository()->createQueryBuilder('t')
            ->delete()
            ->where('t.expiresAt <= :time')
            ->setParameter('time', new \DateTimeImmutable('-1 week'), Types::DATETIME_IMMUTABLE)
            ->getQuery();

        return $query->execute();
    }

    /** @phpstan-return EntityRepository<ResetPasswordRequestInterface> */
    private function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository($this->class);
    }
}
