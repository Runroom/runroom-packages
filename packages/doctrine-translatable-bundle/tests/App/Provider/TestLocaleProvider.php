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

namespace Runroom\DoctrineTranslatableBundle\Tests\Provider;

use Runroom\DoctrineTranslatableBundle\Provider\LocaleProviderInterface;

final class TestLocaleProvider implements LocaleProviderInterface
{
    public function provideCurrentLocale(): ?string
    {
        return 'en';
    }

    public function provideFallbackLocale(): ?string
    {
        return 'en';
    }
}
