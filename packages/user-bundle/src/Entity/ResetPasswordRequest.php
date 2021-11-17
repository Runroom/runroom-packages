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

use Doctrine\ORM\Mapping as ORM;
use Runroom\UserBundle\Model\ResetPasswordRequestInterface;
use Runroom\UserBundle\Model\UserInterface;

/** @ORM\Entity */
class ResetPasswordRequest implements ResetPasswordRequestInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private UserInterface $user;

    /** @ORM\Column(type="string", length=20) */
    private string $selector;

    /** @ORM\Column(type="string", length=100) */
    private string $hashedToken;

    /** @ORM\Column(type="datetime_immutable") */
    private \DateTimeImmutable $requestedAt;

    /** @ORM\Column(type="datetime_immutable") */
    private \DateTimeInterface $expiresAt;

    public function __construct(UserInterface $user, \DateTimeInterface $expiresAt, string $selector, string $hashedToken)
    {
        $this->user = $user;
        $this->requestedAt = new \DateTimeImmutable('now');
        $this->expiresAt = $expiresAt;
        $this->selector = $selector;
        $this->hashedToken = $hashedToken;
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
