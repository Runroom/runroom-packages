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

/**
 * @extends SonataAdminTestCase<Test>
 */
class TestAdminTest extends SonataAdminTestCase
{
    public function testItDoesNotHaveDeleteRoute(): void
    {
        $this->assertAdminRoutesDoesContainRoute('create');
        $this->assertAdminRoutesDoesNotContainRoute('delete');
    }

    public function testItHasAllFilterFields(): void
    {
        $this->assertAdminFilterContainsField('id');
    }

    public function testItHasAllListFields(): void
    {
        $this->assertAdminListContainsField('id');
    }

    public function testItHasAllShowFields(): void
    {
        $this->assertAdminShowContainsField('id');
    }

    public function testItHasAllFormFields(): void
    {
        $this->assertAdminFormContainsField('id');
    }

    public function testItHasAllExportFields(): void
    {
        $this->assertAdminExportDoesContainField('id');
        $this->assertAdminExportDoesNotContainField('random_field');
    }

    protected function getAdminClass(): string
    {
        return TestAdmin::class;
    }
}
