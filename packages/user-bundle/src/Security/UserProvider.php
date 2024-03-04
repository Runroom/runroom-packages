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

namespace Runroom\UserBundle\Security;

use Runroom\UserBundle\Model\UserInterface;
use Runroom\UserBundle\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @phpstan-implements UserProviderInterface<UserInterface>
 */
final class UserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    public function __construct(private readonly UserRepositoryInterface $userRepository) {}

    /**
     * @param string $username
     */
    public function loadUserByUsername($username): SymfonyUserInterface
    {
        return $this->loadUserByIdentifier($username);
    }

    public function loadUserByIdentifier(string $identifier): SymfonyUserInterface
    {
        $user = $this->userRepository->loadUserByIdentifier($identifier);

        if (null === $user || !$user->getEnabled()) {
            throw new UserNotFoundException(sprintf('User "%s" not found.', $identifier));
        }

        return $user;
    }

    public function refreshUser(SymfonyUserInterface $user): SymfonyUserInterface
    {
        if (!$user instanceof UserInterface) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $userIdentifier = $user->getUserIdentifier();

        $refreshedUser = $this->userRepository->loadUserByIdentifier($userIdentifier);

        if (null === $refreshedUser) {
            throw new UserNotFoundException(sprintf('User with identifier "%s" not found.', $userIdentifier));
        }

        return $refreshedUser;
    }

    /**
     * @param string $class
     */
    public function supportsClass($class): bool
    {
        return UserInterface::class === $class || is_subclass_of($class, UserInterface::class);
    }

    /**
     * @param PasswordAuthenticatedUserInterface|SymfonyUserInterface $user
     */
    public function upgradePassword(object $user, string $newHashedPassword): void
    {
        if (!$user instanceof UserInterface) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);

        $this->userRepository->save($user);
    }
}
