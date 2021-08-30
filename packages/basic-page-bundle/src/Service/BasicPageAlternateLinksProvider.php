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

use Runroom\BasicPageBundle\ViewModel\BasicPageViewModel;
use Runroom\SeoBundle\AlternateLinks\AbstractAlternateLinksProvider;

final class BasicPageAlternateLinksProvider extends AbstractAlternateLinksProvider
{
    /** @psalm-suppress MissingTemplateParam, InvalidTemplateParam, InvalidArgument getTranslations misses the correct template parameters */
    public function canGenerateAlternateLink(array $context, string $locale): bool
    {
        if (!isset($context['model']) || !$context['model'] instanceof BasicPageViewModel) {
            return false;
        }

        return $context['model']->getBasicPage()->getTranslations()->containsKey($locale);
    }

    public function getParameters(array $context, string $locale): ?array
    {
        if (!isset($context['model']) || !$context['model'] instanceof BasicPageViewModel) {
            return null;
        }

        return ['slug' => $context['model']->getBasicPage()->getSlug($locale)];
    }

    protected function getRoutes(): array
    {
        return ['runroom.basic_page.route.show'];
    }
}
