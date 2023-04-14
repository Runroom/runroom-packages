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

namespace Runroom\CookiesBundle\Tests\Integration;

use Runroom\CookiesBundle\Entity\CookiesPage;
use Runroom\Testing\TestCase\SonataAdminTestCase;

/**
 * @extends SonataAdminTestCase<CookiesPage>
 */
class CookiesPageAdminTest extends SonataAdminTestCase
{
    public function testItHasAllFormFields(): void
    {
        $this->assertAdminFormContainsField('translations');
    }

    public function testItDoesNotHaveDisabledRoutes(): void
    {
        $this->assertAdminRoutesDoesContainRoute('edit');
        $this->assertAdminRoutesDoesNotContainRoute('show');
        $this->assertAdminRoutesDoesNotContainRoute('delete');
        $this->assertAdminRoutesDoesNotContainRoute('list');
        $this->assertAdminRoutesDoesNotContainRoute('batch');
        $this->assertAdminRoutesDoesNotContainRoute('export');
    }

    protected function getAdminClass(): string
    {
        return 'runroom.cookies.admin.cookies_page';
    }
}
