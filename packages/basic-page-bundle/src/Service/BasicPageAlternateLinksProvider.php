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

use Doctrine\Common\Collections\Collection;
use Runroom\BasicPageBundle\Entity\BasicPageTranslation;
use Runroom\BasicPageBundle\ViewModel\BasicPageViewModel;
use Runroom\SeoBundle\AlternateLinks\AbstractAlternateLinksProvider;
use Runroom\SeoBundle\Model\SeoModelInterface;

/** @phpstan-extends AbstractAlternateLinksProvider<BasicPageViewModel> */
final class BasicPageAlternateLinksProvider extends AbstractAlternateLinksProvider
{
    public function canGenerateAlternateLink(SeoModelInterface $model, string $locale): bool
    {
        $basicPage = $model->getBasicPage();

        if (null === $basicPage) {
            return false;
        }

        /** @var Collection<string, BasicPageTranslation> */
        $translations = $basicPage->getTranslations();

        return $translations->containsKey($locale);
    }

    public function getParameters(SeoModelInterface $model, string $locale): ?array
    {
        $basicPage = $model->getBasicPage();

        if (null === $basicPage) {
            return null;
        }

        return [
            'slug' => $basicPage->getSlug($locale),
        ];
    }

    protected function getRoutes(): array
    {
        return ['runroom.basic_page.route.show'];
    }
}
