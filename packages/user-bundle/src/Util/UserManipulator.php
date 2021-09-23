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
use Runroom\UserBundle\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;

final class UserManipulator
{
    private UserRepository $userRepository;
    private UserPasswordHasher $passwordHasher;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordHasher $passwordHasher
    ) {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    public function create(string $email, string $password, bool $active): void
    {
        $user = $this->userRepository->create();

        $user->setEmail($email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $user->setEnabled($active);

        $this->userRepository->save($user);
    }

    public function activate(string $email): void
    {
        $user = $this->findUserByUsernameOrThrowException($email);
        $user->setEnabled(true);

        $this->userRepository->save($user);
    }

    public function deactivate(string $email): void
    {
        $user = $this->findUserByUsernameOrThrowException($email);
        $user->setEnabled(false);

        $this->userRepository->save($user);
    }

    public function changePassword(string $email, string $password): void
    {
        $user = $this->findUserByUsernameOrThrowException($email);

        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        $this->userRepository->save($user);
    }

    /** @throws \InvalidArgumentException When user does not exist */
    private function findUserByUsernameOrThrowException(string $email): UserInterface
    {
        $user = $this->userRepository->loadUserByIdentifier($email);
        \assert(null === $user || $user instanceof UserInterface);

        if (null === $user) {
            throw new \InvalidArgumentException(sprintf('User identified by "%s" username does not exist.', $email));
        }

        return $user;
    }
}
