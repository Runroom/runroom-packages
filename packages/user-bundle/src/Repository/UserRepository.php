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

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Runroom\UserBundle\Entity\User;
use Runroom\UserBundle\Model\UserInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

/** @extends ServiceEntityRepository<UserInterface> */
final class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof UserInterface) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function loadUserByIdentifier(string $identifier): ?SymfonyUserInterface
    {
        $query = $this->createQueryBuilder('user')
            ->where('user.email = :email')
            ->andWhere('user.enabled = :enabled')
            ->setParameter('email', $identifier)
            ->setParameter('enabled', true)
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    public function loadUserByUsername(string $username): ?SymfonyUserInterface
    {
        return $this->loadUserByIdentifier($username);
    }

    public function create(): UserInterface
    {
        return new User();
    }

    public function save(UserInterface $user): void
    {
        $this->_em->persist($user);
        $this->_em->flush();
    }
}
