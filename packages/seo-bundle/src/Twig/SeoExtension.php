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

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SeoExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('build_alternate_links', [SeoRuntime::class, 'buildAlternateLinks'], [
                'needs_context' => true,
            ]),
            new TwigFunction('build_meta_information', [SeoRuntime::class, 'buildMetaInformation'], [
                'needs_context' => true,
            ]),
        ];
    }
}
