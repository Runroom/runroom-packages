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

namespace Runroom\UserBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class RolesMatrixExtension extends AbstractExtension
{
    #[\Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction('renderMatrix', [RolesMatrixRuntime::class, 'renderMatrix']),
            new TwigFunction('renderRolesList', [RolesMatrixRuntime::class, 'renderRolesList']),
        ];
    }
}
