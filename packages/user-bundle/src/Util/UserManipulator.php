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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class UserManipulator
{
    private UserRepository $userRepository;

    /**
     * @todo: Simplify this when dropping support for Symfony 4
     *
     * @var UserPasswordHasherInterface|UserPasswordEncoderInterface
     */
    private object $passwordHasher;

    /**
     * @todo: Simplify this when dropping support for Symfony 4
     *
     * @param UserPasswordHasherInterface|UserPasswordEncoderInterface $passwordHasher
     */
    public function __construct(
        UserRepository $userRepository,
        object $passwordHasher
    ) {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    public function create(string $email, string $password, bool $active): void
    {
        $user = $this->userRepository->create();

        $user->setEmail($email);

        /* @todo: Simplify this when dropping support for Symfony 4 */
        if ($this->passwordHasher instanceof UserPasswordHasherInterface) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        } else {
            $hashedPassword = $this->passwordHasher->encodePassword($user, $password);
        }

        $user->setPassword($hashedPassword);
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

        /* @todo: Simplify this when dropping support for Symfony 4 */
        if ($this->passwordHasher instanceof UserPasswordHasherInterface) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        } else {
            $hashedPassword = $this->passwordHasher->encodePassword($user, $password);
        }

        $user->setPassword($hashedPassword);

        $this->userRepository->save($user);
    }

    /** @throws \InvalidArgumentException When user does not exist */
    private function findUserByUsernameOrThrowException(string $email): UserInterface
    {
        $user = $this->userRepository->loadUserByIdentifier($email);

        if (null === $user) {
            throw new \InvalidArgumentException(sprintf('User identified by "%s" username does not exist.', $email));
        }

        return $user;
    }
}
