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

namespace Runroom\SeoBundle\Tests\Fixtures;

use Runroom\SeoBundle\Entity\MetaInformation;

class MetaInformationFixture
{
    public const ROUTE = 'route';
    public const ROUTE_NAME = 'route name';
    public const IMAGE = null;
    public const TITLE = '[placeholder] title';
    public const DESCRIPTION = '[missing] description';

    public static function create(): MetaInformation
    {
        $metaInformation = new MetaInformation();

        $metaInformation->setRoute(self::ROUTE);
        $metaInformation->setRouteName(self::ROUTE_NAME);
        $metaInformation->setImage(self::IMAGE);
        $metaInformation->translate()->setTitle(self::TITLE);
        $metaInformation->translate()->setDescription(self::DESCRIPTION);

        return $metaInformation;
    }
}
