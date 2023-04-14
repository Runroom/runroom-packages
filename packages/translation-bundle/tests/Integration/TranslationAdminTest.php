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
use Runroom\TranslationBundle\Entity\Translation;

/**
 * @extends SonataAdminTestCase<Translation>
 */
class TranslationAdminTest extends SonataAdminTestCase
{
    public function testItHasAllListFields(): void
    {
        $this->assertAdminListContainsField('key');
        $this->assertAdminListContainsField('value');
    }

    public function testItHasAllFormFields(): void
    {
        $this->assertAdminFormContainsField('key');
        $this->assertAdminFormContainsField('translations');
    }

    public function testItHasAllFilterFields(): void
    {
        $this->assertAdminFilterContainsField('key');
        $this->assertAdminFilterContainsField('translations.value');
    }

    public function testItDoesNotHaveDisabledRoutes(): void
    {
        $this->assertAdminRoutesDoesNotContainRoute('create');
        $this->assertAdminRoutesDoesNotContainRoute('delete');
    }

    public function testItDoesDefineDefaultFilterParameters(): void
    {
        $this->assertAdminFilterParametersContainsFilter('_sort_by', 'key');
    }

    protected function getAdminClass(): string
    {
        return 'runroom.translation.admin.translation';
    }
}
