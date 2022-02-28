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

use Runroom\SeoBundle\Admin\MetaInformationAdmin;
use Runroom\SeoBundle\Entity\MetaInformation;
use Runroom\Testing\TestCase\SonataAdminTestCase;

/** @extends SonataAdminTestCase<MetaInformation> */
class MetaInformationAdminTest extends SonataAdminTestCase
{
    /**
     * @test
     */
    public function itHasAllListFields(): void
    {
        $this->assertAdminListContainsField('routeName');
        $this->assertAdminListContainsField('title');
        $this->assertAdminListContainsField('description');
    }

    /**
     * @test
     */
    public function itHasAllFormFields(): void
    {
        $this->assertAdminFormContainsField('translations');
        $this->assertAdminFormContainsField('image');
    }

    /**
     * @test
     */
    public function itHasAllFilterFields(): void
    {
        $this->assertAdminFilterContainsField('routeName');
    }

    /**
     * @test
     */
    public function itDoesNotHaveDisabledRoutes(): void
    {
        $this->assertAdminRoutesDoesNotContainRoute('create');
        $this->assertAdminRoutesDoesNotContainRoute('delete');
    }

    /**
     * @test
     */
    public function itDoesDefineDefaultFilterParameters(): void
    {
        $this->assertAdminFilterParametersContainsFilter('_sort_by', 'routeName');
    }

    protected function getAdminClass(): string
    {
        return MetaInformationAdmin::class;
    }
}
