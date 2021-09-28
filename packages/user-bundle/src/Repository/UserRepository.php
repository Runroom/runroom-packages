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

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Runroom\UserBundle\Model\UserInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

final class UserRepository implements PasswordUpgraderInterface, UserLoaderInterface
{
    private EntityManagerInterface $entityManager;

    /** @phpstan-var class-string<UserInterface> */
    private string $class;

    /** @phpstan-param class-string<UserInterface> $class */
    public function __construct(EntityManagerInterface $entityManager, string $class)
    {
        $this->entityManager = $entityManager;
        $this->class = $class;
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof UserInterface) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function loadUserByIdentifier(string $identifier): ?SymfonyUserInterface
    {
        return $this->getRepository()->findOneBy([
            'email' => $identifier,
            'enabled' => true,
        ]);
    }

    public function loadUserByUsername(string $username): ?SymfonyUserInterface
    {
        return $this->loadUserByIdentifier($username);
    }

    public function create(): UserInterface
    {
        return new $this->class();
    }

    public function save(UserInterface $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /** @phpstan-return EntityRepository<UserInterface> */
    private function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository($this->class);
    }
}
