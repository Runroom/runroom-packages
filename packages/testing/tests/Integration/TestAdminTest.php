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

namespace Runroom\Testing\Tests\Integration;

use Runroom\Testing\TestCase\SonataAdminTestCase;
use Runroom\Testing\Tests\App\Admin\TestAdmin;
use Runroom\Testing\Tests\App\Entity\Test;

/** @extends SonataAdminTestCase<Test> */
class TestAdminTest extends SonataAdminTestCase
{
    /** @test */
    public function itDoesNotHaveDeleteRoute(): void
    {
        $this->assertAdminRoutesDoesContainRoute('create');
        $this->assertAdminRoutesDoesNotContainRoute('delete');
    }

    /** @test */
    public function itHasAllFilterFields(): void
    {
        $this->assertAdminFilterContainsField('id');
    }

    /** @test */
    public function itHasAllListFields(): void
    {
        $this->assertAdminListContainsField('id');
    }

    /** @test */
    public function itHasAllShowFields(): void
    {
        $this->assertAdminShowContainsField('id');
    }

    /** @test */
    public function itHasAllFormFields(): void
    {
        $this->assertAdminFormContainsField('id');
    }

    /** @test */
    public function itHasAllExportFields(): void
    {
        $this->assertAdminExportDoesContainField('id');
        $this->assertAdminExportDoesNotContainField('random_field');
    }

    protected function getAdminClass(): string
    {
        return TestAdmin::class;
    }
}
