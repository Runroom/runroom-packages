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

namespace Runroom\SeoBundle\Tests\App\AlternateLinks;

use Runroom\SeoBundle\AlternateLinks\AbstractAlternateLinksProvider;

final class DummyAlternateLinksProvider extends AbstractAlternateLinksProvider
{
    public function canGenerateAlternateLink(array $context, string $locale): bool
    {
        return true;
    }

    public function getParameters(array $context, string $locale): array
    {
        return [
            'dummy_param' => 'dummy_value',
            'dummy_query' => 'dummy_value',
        ];
    }

    /**
     * @return string[]
     */
    protected function getRoutes(): array
    {
        return ['dummy_route'];
    }
}
