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

final readonly class UserRepository implements UserRepositoryInterface
{
    /**
     * @phpstan-param class-string<UserInterface> $class
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private string $class,
    ) {}

    public function loadUserByIdentifier(string $identifier): ?UserInterface
    {
        return $this->getRepository()->findOneBy(['email' => $identifier]);
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

    /**
     * @phpstan-return EntityRepository<UserInterface>
     */
    private function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository($this->class);
    }
}
