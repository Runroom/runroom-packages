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
use Runroom\UserBundle\Model\UserInterface;

/** @extends SonataAdminTestCase<UserInterface> */
class UserAdminTest extends SonataAdminTestCase
{
    /** @test */
    public function itHasAllListFields(): void
    {
        $this->assertAdminListContainsField('createdAt');
        $this->assertAdminListContainsField('email');
        $this->assertAdminListContainsField('enabled');
    }

    /** @test */
    public function itHasAllFormFields(): void
    {
        $this->assertAdminFormContainsField('email');
        $this->assertAdminFormContainsField('plainPassword');
        $this->assertAdminFormContainsField('enabled');
        $this->assertAdminFormContainsField('roles');
    }

    /** @test */
    public function itHasAllFilterFields(): void
    {
        $this->assertAdminFilterContainsField('email');
        $this->assertAdminFilterContainsField('enabled');
    }

    /** @test */
    public function itDoesNotHaveDisabledRoutes(): void
    {
        $this->assertAdminRoutesDoesNotContainRoute('show');
    }

    protected function getAdminClass(): string
    {
        return 'runroom_user.admin.user';
    }
}
