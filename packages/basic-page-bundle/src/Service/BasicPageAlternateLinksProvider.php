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

namespace Runroom\BasicPageBundle\Service;

use Runroom\SeoBundle\AlternateLinks\AbstractAlternateLinksProvider;

final class BasicPageAlternateLinksProvider extends AbstractAlternateLinksProvider
{
    public function getAvailableLocales($model): ?array
    {
        return $model->getBasicPage()->getTranslations()->getKeys();
    }

    public function getParameters($model, string $locale): ?array
    {
        return [
            'slug' => $model->getBasicPage()->getSlug($locale),
        ];
    }

    protected function getRoutes(): array
    {
        return ['runroom.basic_page.route.show'];
    }
}
