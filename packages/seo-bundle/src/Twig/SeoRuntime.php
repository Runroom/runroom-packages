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

namespace Runroom\SeoBundle\Twig;

use Runroom\SeoBundle\AlternateLinks\AlternateLinksServiceInterface;
use Runroom\SeoBundle\MetaInformation\MetaInformationServiceInterface;
use Runroom\SeoBundle\ViewModel\MetaInformationViewModel;
use Twig\Extension\RuntimeExtensionInterface;

class SeoRuntime implements RuntimeExtensionInterface
{
    private AlternateLinksServiceInterface $alternateLinks;
    private MetaInformationServiceInterface $metaInformation;

    public function __construct(
        AlternateLinksServiceInterface $alternateLinks,
        MetaInformationServiceInterface $metaInformation
    ) {
        $this->alternateLinks = $alternateLinks;
        $this->metaInformation = $metaInformation;
    }

    /**
     * @param array<string, mixed> $context
     *
     * @return array<string, string>|null
     */
    public function buildAlternateLinks(array $context): ?array
    {
        return $this->alternateLinks->build($context);
    }

    /** @param array<string, mixed> $context */
    public function buildMetaInformation(array $context): ?MetaInformationViewModel
    {
        return $this->metaInformation->build($context);
    }
}
