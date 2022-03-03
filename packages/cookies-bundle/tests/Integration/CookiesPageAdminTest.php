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

use Runroom\CookiesBundle\Admin\CookiesPageAdmin;
use Runroom\CookiesBundle\Entity\CookiesPage;
use Runroom\Testing\TestCase\SonataAdminTestCase;

/**
 * @extends SonataAdminTestCase<CookiesPage>
 */
class CookiesPageAdminTest extends SonataAdminTestCase
{
    /**
     * @test
     */
    public function itHasAllFormFields(): void
    {
        $this->assertAdminFormContainsField('translations');
    }

    /**
     * @test
     */
    public function itDoesNotHaveDisabledRoutes(): void
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
        return CookiesPageAdmin::class;
    }
}
