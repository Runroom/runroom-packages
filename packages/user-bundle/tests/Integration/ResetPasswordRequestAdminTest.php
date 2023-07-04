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

namespace Runroom\UserBundle\Tests\Integration;

use Runroom\Testing\TestCase\SonataAdminTestCase;
use Runroom\UserBundle\Entity\ResetPasswordRequest;

/**
 * @extends SonataAdminTestCase<ResetPasswordRequest>
 */
final class ResetPasswordRequestAdminTest extends SonataAdminTestCase
{
    public function testItHasAllListFields(): void
    {
        $this->assertAdminListContainsField('hashedToken');
        $this->assertAdminListContainsField('requestedAt');
        $this->assertAdminListContainsField('expiresAt');
        $this->assertAdminListContainsField('user');
    }

    public function testItHasAllFilterFields(): void
    {
        $this->assertAdminFilterContainsField('user');
    }

    public function testItDoesNotHaveDisabledRoutes(): void
    {
        $this->assertAdminRoutesDoesNotContainRoute('create');
        $this->assertAdminRoutesDoesNotContainRoute('edit');
        $this->assertAdminRoutesDoesNotContainRoute('show');
    }

    protected function getAdminClass(): string
    {
        return 'runroom.user.admin.reset_password_request';
    }
}
