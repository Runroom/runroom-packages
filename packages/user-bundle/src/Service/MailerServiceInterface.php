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

namespace Runroom\UserBundle\Service;

use Runroom\UserBundle\Model\UserInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;

interface MailerServiceInterface
{
    public function sendResetPasswordEmail(UserInterface $user, ResetPasswordToken $resetToken): void;
}
