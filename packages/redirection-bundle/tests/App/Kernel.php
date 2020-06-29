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

namespace Runroom\RedirectionBundle\Tests\App;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Fidry\AliceDataFixtures\Bridge\Symfony\FidryAliceDataFixturesBundle;
use Nelmio\Alice\Bridge\Symfony\NelmioAliceBundle;
use Runroom\RedirectionBundle\RunroomRedirectionBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        return [
            new DoctrineBundle(),
            new FidryAliceDataFixturesBundle(),
            new FrameworkBundle(),
            new NelmioAliceBundle(),
            new RunroomRedirectionBundle(),
        ];
    }

    public function getCacheDir(): string
    {
        return $this->getBaseDir() . '/cache';
    }

    public function getLogDir(): string
    {
        return $this->getBaseDir() . '/log';
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->loadFromExtension('framework', [
            'test' => true,
            'router' => ['utf8' => true],
            'secret' => 'secret',
        ]);

        $container->loadFromExtension('doctrine', [
            'dbal' => ['url' => 'sqlite://:memory:'],
            'orm' => [
                'auto_mapping' => true,
                'mappings' => [
                    'Entity' => [
                        'type' => 'annotation',
                        'dir' => '%kernel.project_dir%/Entity',
                        'prefix' => 'Runroom\RedirectionBundle\Tests\App\Entity',
                        'is_bundle' => false,
                    ],
                ],
            ],
        ]);

        $container->loadFromExtension('runroom_redirection', [
            'enable_automatic_redirections' => true,
            'automatic_redirections' => [
                Entity\Entity::class => [
                    'route' => 'route.entity',
                    'routeParameters' => ['slug' => 'slug'],
                ],
                Entity\WrongEntity::class => [
                    'route' => 'route.missing',
                    'routeParameters' => ['slug' => 'slug'],
                ],
            ],
        ]);
    }

    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
        $routes->add('/entity/{slug}', 'controller', 'route.entity');
    }

    private function getBaseDir(): string
    {
        return sys_get_temp_dir() . '/runroom-redirection-bundle/var';
    }
}
