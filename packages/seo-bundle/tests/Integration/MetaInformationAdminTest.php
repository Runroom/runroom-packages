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

namespace Runroom\SeoBundle\Tests\Integration;

use Runroom\SeoBundle\Entity\MetaInformation;
use Runroom\Testing\TestCase\SonataAdminTestCase;

/**
 * @extends SonataAdminTestCase<MetaInformation>
 */
class MetaInformationAdminTest extends SonataAdminTestCase
{
    public function testItHasAllListFields(): void
    {
        $this->assertAdminListContainsField('routeName');
        $this->assertAdminListContainsField('title');
        $this->assertAdminListContainsField('description');
    }

    public function testItHasAllFormFields(): void
    {
        $this->assertAdminFormContainsField('translations');
        $this->assertAdminFormContainsField('image');
    }

    public function testItHasAllFilterFields(): void
    {
        $this->assertAdminFilterContainsField('routeName');
    }

    public function testItDoesNotHaveDisabledRoutes(): void
    {
        $this->assertAdminRoutesDoesNotContainRoute('create');
        $this->assertAdminRoutesDoesNotContainRoute('delete');
    }

    public function testItDoesDefineDefaultFilterParameters(): void
    {
        $this->assertAdminFilterParametersContainsFilter('_sort_by', 'routeName');
    }

    protected function getAdminClass(): string
    {
        return 'runroom.seo.admin.meta_information';
    }
}
