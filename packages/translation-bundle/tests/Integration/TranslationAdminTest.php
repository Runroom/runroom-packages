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

namespace Runroom\TranslationBundle\Tests\Integration;

use Runroom\Testing\TestCase\SonataAdminTestCase;
use Runroom\TranslationBundle\Admin\TranslationAdmin;

class TranslationAdminTest extends SonataAdminTestCase
{
    /** @test */
    public function itHasAllListFields(): void
    {
        $this->assertAdminListContainsField('key');
        $this->assertAdminListContainsField('value');
    }

    /** @test */
    public function itHasAllFormFields(): void
    {
        $this->assertAdminFormContainsField('key');
        $this->assertAdminFormContainsField('translations');
    }

    /** @test */
    public function itHasAllFilterFields(): void
    {
        $this->assertAdminFilterContainsField('key');
        $this->assertAdminFilterContainsField('translations.value');
    }

    /** @test */
    public function itDoesNotHaveDisabledRoutes(): void
    {
        $this->assertAdminRoutesDoesNotContainRoute('create');
        $this->assertAdminRoutesDoesNotContainRoute('delete');
    }

    /** @test */
    public function itDoesDefineDefaultFilterParameters(): void
    {
        $this->assertAdminFilterParametersContainsFilter('_sort_by', 'key');
    }

    protected function getAdminClass(): string
    {
        return TranslationAdmin::class;
    }
}
