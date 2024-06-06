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

use Runroom\SeoBundle\Entity\EntityMetaInformation;
use Runroom\Testing\TestCase\SonataAdminTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * @extends SonataAdminTestCase<EntityMetaInformation>
 */
final class EntityMetaInformationAdminTest extends SonataAdminTestCase
{
    use ResetDatabase;

    public function testItHasAllFormFields(): void
    {
        $this->assertAdminFormContainsField('translations');
    }

    protected function getAdminClass(): string
    {
        return 'runroom.seo.admin.entity_meta_information';
    }
}
