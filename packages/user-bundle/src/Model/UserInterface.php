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

namespace Runroom\UserBundle\Model;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

interface UserInterface extends SymfonyUserInterface, PasswordAuthenticatedUserInterface
{
    public const ROLE_DEFAULT = 'ROLE_USER';

    /**
     * @return int|string|null
     */
    public function getId();

    public function getUserIdentifier(): string;

    public function getEmail(): ?string;

    public function setEmail(?string $email): self;

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): self;

    public function setPassword(?string $password): self;

    public function getPlainPassword(): ?string;

    public function setPlainPassword(?string $plainPassword): self;

    public function getEnabled(): bool;

    public function setEnabled(bool $enabled): self;

    public function getCreatedAt(): ?\DateTimeInterface;

    public function setCreatedAt(?\DateTimeInterface $createdAt): self;
}
