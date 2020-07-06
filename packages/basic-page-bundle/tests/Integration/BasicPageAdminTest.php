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

namespace Runroom\BasicPageBundle\Tests\Integration;

use Runroom\BasicPageBundle\Admin\BasicPageAdmin;
use Runroom\Testing\TestCase\SonataAdminTestCase;

class BasicPageAdminTest extends SonataAdminTestCase
{
    /** @test */
    public function itHasAllListFields(): void
    {
        $this->assertAdminListContainsField('title');
        $this->assertAdminListContainsField('location');
        $this->assertAdminListContainsField('publish');
        $this->assertAdminListContainsField('_action');
    }

    /** @test */
    public function itHasAllFormFields(): void
    {
        $this->assertAdminFormContainsField('translations');
        $this->assertAdminFormContainsField('publish');
        $this->assertAdminFormContainsField('location');
        $this->assertAdminFormContainsField('metaInformation');
    }

    /** @test */
    public function itHasAllFilterFields(): void
    {
        $this->assertAdminFilterContainsField('translations.title');
        $this->assertAdminFilterContainsField('publish');
    }

    protected function getAdminClass(): string
    {
        return BasicPageAdmin::class;
    }
}
