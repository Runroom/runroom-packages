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

/** @extends SonataAdminTestCase<ResetPasswordRequest> */
class ResetPasswordRequestAdminTest extends SonataAdminTestCase
{
    /** @test */
    public function itHasAllListFields(): void
    {
        $this->assertAdminListContainsField('hashedToken');
        $this->assertAdminListContainsField('requestedAt');
        $this->assertAdminListContainsField('expiresAt');
        $this->assertAdminListContainsField('user');
    }

    /** @test */
    public function itHasAllFilterFields(): void
    {
        $this->assertAdminFilterContainsField('user');
    }

    /** @test */
    public function itDoesNotHaveDisabledRoutes(): void
    {
        $this->assertAdminRoutesDoesNotContainRoute('create');
        $this->assertAdminRoutesDoesNotContainRoute('edit');
        $this->assertAdminRoutesDoesNotContainRoute('show');
    }

    protected function getAdminClass(): string
    {
        return 'runroom_user.admin.reset_password_request';
    }
}
