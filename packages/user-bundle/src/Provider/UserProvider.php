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

namespace Runroom\UserBundle\Provider;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Runroom\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class UserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    private ObjectManager $objectManager;

    /** @phpstan-var class-string<UserInterface> $class */
    private string $class;

    /** @phpstan-param class-string<UserInterface> $class */
    public function __construct(ObjectManager $objectManager, string $class)
    {
        $this->objectManager = $objectManager;
        $this->class = $class;
    }

    /** @param string $username */
    public function loadUserByUsername($username): SymfonyUserInterface
    {
        return $this->loadUserByIdentifier($username);
    }

    public function loadUserByIdentifier(string $identifier): SymfonyUserInterface
    {
        $user = $this->getRepository()->findOneBy([
            'email' => $identifier,
            'enabled' => true,
        ]);

        if (null === $user) {
            throw $this->buildUserNotFoundException(sprintf('User "%s" not found.', $identifier), $identifier);
        }

        return $user;
    }

    public function refreshUser(SymfonyUserInterface $user): SymfonyUserInterface
    {
        if (!$user instanceof $this->class) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_debug_type($user)));
        }

        $id = $this->objectManager->getClassMetadata($this->class)->getIdentifierValues($user);
        $refreshedUser = $this->getRepository()->find($id);

        if (null === $refreshedUser) {
            $identifier = json_encode($id);
            $identifier = false === $identifier ? '' : $identifier;

            throw $this->buildUserNotFoundException(sprintf('User with id "%s" not found.', $identifier), $identifier);
        }

        return $refreshedUser;
    }

    /** @param string $class */
    public function supportsClass($class): bool
    {
        return UserInterface::class === $class || is_subclass_of($class, UserInterface::class);
    }

    /** @param UserInterface $user */
    public function upgradePassword($user, string $newHashedPassword): void
    {
        if (!$user instanceof UserInterface) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_debug_type($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->objectManager->persist($user);
        $this->objectManager->flush();
    }

    /** @phpstan-return ObjectRepository<UserInterface> */
    private function getRepository(): ObjectRepository
    {
        return $this->objectManager->getRepository($this->class);
    }

    /** @todo: Simplify when dropping support for Symfony 4 */
    private function buildUserNotFoundException(string $message, string $identifier): \Exception
    {
        if (!class_exists(UserNotFoundException::class)) {
            $exception = new UsernameNotFoundException($message);
            $exception->setUsername($identifier);

            return $exception;
        }

        $exception = new UserNotFoundException($message);
        $exception->setUserIdentifier($identifier);

        return $exception;
    }
}
