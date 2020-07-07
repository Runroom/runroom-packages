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

namespace Runroom\RedirectionBundle\Tests\Integration;

use Runroom\RedirectionBundle\Admin\RedirectAdmin;
use Runroom\Testing\TestCase\SonataAdminTestCase;

class RedirectAdminTest extends SonataAdminTestCase
{
    /** @test */
    public function itHasAllListFields(): void
    {
        $this->assertAdminListContainsField('source');
        $this->assertAdminListContainsField('destination');
        $this->assertAdminListContainsField('httpCode');
        $this->assertAdminListContainsField('publish');
        $this->assertAdminListContainsField('automatic');
        $this->assertAdminListContainsField('_action');
    }

    /** @test */
    public function itHasAllFormFields(): void
    {
        $this->assertAdminFormContainsField('source');
        $this->assertAdminFormContainsField('destination');
        $this->assertAdminFormContainsField('httpCode');
        $this->assertAdminFormContainsField('publish');
        $this->assertAdminFormContainsField('automatic');
    }

    /** @test */
    public function itHasAllFilterFields(): void
    {
        $this->assertAdminFilterContainsField('source');
        $this->assertAdminFilterContainsField('destination');
        $this->assertAdminFilterContainsField('httpCode');
        $this->assertAdminFilterContainsField('automatic');
        $this->assertAdminFilterContainsField('publish');
    }

    protected function getAdminClass(): string
    {
        return RedirectAdmin::class;
    }
}
