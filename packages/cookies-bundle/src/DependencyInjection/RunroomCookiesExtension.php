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

namespace Runroom\CookiesBundle\DependencyInjection;

use Runroom\CookiesBundle\Service\CookiesPageService;
use Runroom\CookiesBundle\Twig\CookiesRuntime;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class RunroomCookiesExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.php');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition(CookiesRuntime::class);
        $definition->setArgument('$cookies', $config['cookies']);

        $definition = $container->getDefinition(CookiesPageService::class);
        $definition->setArgument('$cookies', $config['cookies']);
    }
}
