<?php

/*
 * This file is part of the Runroom package.
 *
 * (c) Runroom <runroom@runroom.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Runroom\SeoBundle\Tests\Fixtures;

use Runroom\SeoBundle\Entity\EntityMetaInformation;

class EntityMetaInformationFixture
{
    public const ID = 1;
    public const TITLE = 'title';
    public const DESCRIPTION = 'description';

    public static function create(): EntityMetaInformation
    {
        $metaInformation = new EntityMetaInformation();

        $metaInformation->setId(self::ID);
        $metaInformation->translate()->setTitle(self::TITLE);
        $metaInformation->translate()->setDescription(self::DESCRIPTION);

        return $metaInformation;
    }
}
