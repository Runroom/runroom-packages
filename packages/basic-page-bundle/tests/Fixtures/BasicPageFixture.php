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

namespace Runroom\BasicPageBundle\Tests\Fixtures;

use Runroom\BasicPageBundle\Entity\BasicPage;
use Runroom\SeoBundle\Entity\EntityMetaInformation;

class BasicPageFixture
{
    public const ID = 1;
    public const TITLE = 'title';
    public const CONTENT = 'content';
    public const LOCATION = 'none';
    public const SLUG = 'slug';
    public const PUBLISH = true;

    public static function create(): BasicPage
    {
        $basicPage = new BasicPage();

        $basicPage->setId(self::ID);
        $basicPage->setLocation(self::LOCATION);
        $basicPage->translate()->setTitle(self::TITLE);
        $basicPage->translate()->setContent(self::CONTENT);
        $basicPage->translate()->setSlug(self::SLUG);
        $basicPage->setPublish(self::PUBLISH);
        $basicPage->setMetaInformation(new EntityMetaInformation());

        return $basicPage;
    }

    /** @param string[] $locales */
    public static function createWithSlugs(array $locales): BasicPage
    {
        $basicPage = new BasicPage();

        foreach ($locales as $locale) {
            $basicPage->translate($locale)->setSlug('slug_' . $locale);
        }

        $basicPage->mergeNewTranslations();

        return $basicPage;
    }
}
