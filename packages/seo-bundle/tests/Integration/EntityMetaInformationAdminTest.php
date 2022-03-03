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

use Runroom\SeoBundle\Admin\EntityMetaInformationAdmin;
use Runroom\SeoBundle\Entity\EntityMetaInformation;
use Runroom\Testing\TestCase\SonataAdminTestCase;

/**
 * @extends SonataAdminTestCase<EntityMetaInformation>
 */
class EntityMetaInformationAdminTest extends SonataAdminTestCase
{
    /**
     * @test
     */
    public function itHasAllFormFields(): void
    {
        $this->assertAdminFormContainsField('translations');
    }

    protected function getAdminClass(): string
    {
        return EntityMetaInformationAdmin::class;
    }
}
