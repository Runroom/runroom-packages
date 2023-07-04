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

use Runroom\BasicPageBundle\Entity\BasicPage;
use Runroom\Testing\TestCase\SonataAdminTestCase;
use Sonata\AdminBundle\Datagrid\ListMapper;

/**
 * @extends SonataAdminTestCase<BasicPage>
 */
final class BasicPageAdminTest extends SonataAdminTestCase
{
    public function testItHasAllListFields(): void
    {
        $this->assertAdminListContainsField('title');
        $this->assertAdminListContainsField('location');
        $this->assertAdminListContainsField('publish');
        $this->assertAdminListContainsField(ListMapper::NAME_ACTIONS);
    }

    public function testItHasAllFormFields(): void
    {
        $this->assertAdminFormContainsField('translations');
        $this->assertAdminFormContainsField('publish');
        $this->assertAdminFormContainsField('location');
        $this->assertAdminFormContainsField('metaInformation');
    }

    public function testItHasAllFilterFields(): void
    {
        $this->assertAdminFilterContainsField('translations.title');
        $this->assertAdminFilterContainsField('publish');
    }

    protected function getAdminClass(): string
    {
        return 'runroom.basic_page.admin.basic_page';
    }
}
