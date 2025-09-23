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

namespace Runroom\UserBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Runroom\UserBundle\Model\ResetPasswordRequestInterface;
use Runroom\UserBundle\Model\UserInterface;

/**
 * @api
 */
#[ORM\Entity]
class ResetPasswordRequest implements ResetPasswordRequestInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private readonly \DateTimeImmutable $requestedAt;

    public function __construct(
        #[ORM\ManyToOne(targetEntity: User::class)]
        #[ORM\JoinColumn(nullable: false)]
        private readonly UserInterface $user,
        #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
        private readonly \DateTimeInterface $expiresAt,
        #[ORM\Column(type: Types::STRING, length: 20)]
        private readonly string $selector,
        #[ORM\Column(type: Types::STRING, length: 100)]
        private readonly string $hashedToken,
    ) {
        $this->requestedAt = new \DateTimeImmutable('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): object
    {
        return $this->user;
    }

    public function getSelector(): string
    {
        return $this->selector;
    }

    public function getHashedToken(): string
    {
        return $this->hashedToken;
    }

    public function getRequestedAt(): \DateTimeInterface
    {
        return $this->requestedAt;
    }

    public function getExpiresAt(): \DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt->getTimestamp() <= time();
    }
}
