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

namespace Runroom\SeoBundle\MetaInformation;

use Runroom\SeoBundle\ViewModel\MetaInformationViewModel;

interface MetaInformationBuilderInterface
{
    final public const DEFAULT_ROUTE = 'default';

    /**
     * @param array<string, mixed> $context
     */
    public function build(
        MetaInformationProviderInterface $provider,
        array $context,
        string $route,
    ): MetaInformationViewModel;
}
