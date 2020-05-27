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

namespace Runroom\SeoBundle\DependencyInjection\Compiler;

use Runroom\SeoBundle\DependencyInjection\RunroomSeoExtension;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class GlobalVariablesCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('twig')) {
            $container->getDefinition('twig')->addMethodCall('addGlobal', [
                'xDefaultLocale', $container->getParameter(RunroomSeoExtension::XDEFAULT_LOCALE),
            ]);
        }
    }
}
