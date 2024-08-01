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

namespace Runroom\DoctrineTranslatableBundle\Tests\App;

use DAMA\DoctrineTestBundle\DAMADoctrineTestBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Runroom\DoctrineTranslatableBundle\RunroomDoctrineTranslatableBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Zenstruck\Foundry\ZenstruckFoundryBundle;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        return [
            new DAMADoctrineTestBundle(),
            new DoctrineBundle(),
            new FrameworkBundle(),
            new ZenstruckFoundryBundle(),

            new RunroomDoctrineTranslatableBundle(),
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
        $container->setParameter('kernel.default_locale', 'en');

        $container->loadFromExtension('framework', [
            'annotations' => false,
            // 'handle_all_throwables' => true,
            'test' => true,
            'translator' => true,
            'router' => ['utf8' => true],
            'session' => [
                'cookie_secure' => 'auto',
                'cookie_samesite' => 'lax',
                'handler_id' => null,
                'storage_factory_id' => 'session.storage.factory.mock_file',
            ],
            'php_errors' => [
                'log' => true,
            ],
            'secret' => 'secret',
            'http_method_override' => false,
        ]);

        $container->loadFromExtension('doctrine', [
            'dbal' => [
                'url' => 'sqlite:///%kernel.cache_dir%/app.db',
                'logging' => false,
                'use_savepoints' => true,
            ],
            'orm' => [
                'report_fields_where_declared' => true,
                'controller_resolver' => [
                    'auto_mapping' => false,
                ],
                'mappings' => [
                    'entity' => [
                        'type' => 'attribute',
                        'dir' => '%kernel.project_dir%/Entity',
                        'prefix' => 'Runroom\DoctrineTranslatableBundle\Tests\App\Entity',
                        'is_bundle' => false,
                    ],
                    'translatable' => [
                        'type' => 'attribute',
                        'dir' => '%kernel.project_dir%/Entity/Translatable',
                        'prefix' => 'Runroom\DoctrineTranslatableBundle\Tests\App\Entity\Translatable',
                        'is_bundle' => false,
                    ],
                    'translation' => [
                        'type' => 'attribute',
                        'dir' => '%kernel.project_dir%/Entity/Translation',
                        'prefix' => 'Runroom\DoctrineTranslatableBundle\Tests\App\Entity\Translation',
                        'is_bundle' => false,
                    ],
                ],
            ],
        ]);
    }

    protected function configureRoutes(RoutingConfigurator $routes): void {}

    private function getBaseDir(): string
    {
        return sys_get_temp_dir() . '/runroom-doctrine-translatable-bundle/var';
    }
}
