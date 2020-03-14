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

namespace Runroom\TranslationBundle\Tests\Fixtures;

use Runroom\TranslationBundle\Entity\Translation;

class TranslationFixtures
{
    public const KEY = 'my.key';
    public const VALUE = 'My value';

    public static function create(): Translation
    {
        $translation = new Translation();

        $translation->setKey(self::KEY);
        $translation->translate()->setValue(self::VALUE);

        return $translation;
    }
}
