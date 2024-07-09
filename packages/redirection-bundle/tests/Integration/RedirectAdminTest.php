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

use Runroom\RedirectionBundle\Entity\Redirect;
use Runroom\Testing\TestCase\SonataAdminTestCase;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * @extends SonataAdminTestCase<Redirect>
 */
final class RedirectAdminTest extends SonataAdminTestCase
{
    use ResetDatabase;

    public function testItHasAllListFields(): void
    {
        $this->assertAdminListContainsField('source');
        $this->assertAdminListContainsField('destination');
        $this->assertAdminListContainsField('httpCode');
        $this->assertAdminListContainsField('publish');
        $this->assertAdminListContainsField('automatic');
        $this->assertAdminListContainsField(ListMapper::NAME_ACTIONS);
    }

    public function testItHasAllFormFields(): void
    {
        $this->assertAdminFormContainsField('source');
        $this->assertAdminFormContainsField('destination');
        $this->assertAdminFormContainsField('httpCode');
        $this->assertAdminFormContainsField('publish');
    }

    public function testItHasAllFilterFields(): void
    {
        $this->assertAdminFilterContainsField('source');
        $this->assertAdminFilterContainsField('destination');
        $this->assertAdminFilterContainsField('httpCode');
        $this->assertAdminFilterContainsField('automatic');
        $this->assertAdminFilterContainsField('publish');
    }

    protected function getAdminClass(): string
    {
        return 'runroom.redirection.admin.redirect';
    }
}
