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

namespace Runroom\UserBundle\Util;

use Runroom\UserBundle\Model\UserInterface;
use Runroom\UserBundle\Repository\UserRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserManipulator
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {}

    public function create(string $identifier, string $password, bool $active): void
    {
        $user = $this->userRepository->create();

        $user->setEmail($identifier);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);

        $user->setPassword($hashedPassword);
        $user->setEnabled($active);
        $user->setCreatedAt(new \DateTime());

        $this->userRepository->save($user);
    }

    public function activate(string $identifier): void
    {
        $user = $this->findUserByIdentifierOrThrowException($identifier);
        $user->setEnabled(true);

        $this->userRepository->save($user);
    }

    public function deactivate(string $identifier): void
    {
        $user = $this->findUserByIdentifierOrThrowException($identifier);
        $user->setEnabled(false);

        $this->userRepository->save($user);
    }

    public function changePassword(string $identifier, string $password): void
    {
        $user = $this->findUserByIdentifierOrThrowException($identifier);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);

        $user->setPassword($hashedPassword);

        $this->userRepository->save($user);
    }

    /**
     * @throws \InvalidArgumentException When user does not exist
     */
    private function findUserByIdentifierOrThrowException(string $identifier): UserInterface
    {
        $user = $this->userRepository->loadUserByIdentifier($identifier);

        if (null === $user) {
            throw new \InvalidArgumentException(\sprintf('User identified by "%s" username does not exist.', $identifier));
        }

        return $user;
    }
}
