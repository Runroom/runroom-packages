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

namespace Runroom\SortableBehaviorBundle\Tests\App;

use Composer\InstalledVersions;
use DAMA\DoctrineTestBundle\DAMADoctrineTestBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Knp\Bundle\MenuBundle\KnpMenuBundle;
use Runroom\SortableBehaviorBundle\RunroomSortableBehaviorBundle;
use Sonata\AdminBundle\SonataAdminBundle;
use Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\UX\StimulusBundle\StimulusBundle;
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
            new KnpMenuBundle(),
            new SecurityBundle(),
            new SonataAdminBundle(),
            new SonataDoctrineORMAdminBundle(),
            new TwigBundle(),
            new ZenstruckFoundryBundle(),
            new StimulusBundle(),

            new RunroomSortableBehaviorBundle(),
        ];
    }

    #[\Override]
    public function getCacheDir(): string
    {
        return $this->getBaseDir() . '/cache';
    }

    #[\Override]
    public function getLogDir(): string
    {
        return $this->getBaseDir() . '/log';
    }

    #[\Override]
    public function getProjectDir(): string
    {
        return __DIR__;
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $loader->load($this->getProjectDir() . '/services.php');

        $container->loadFromExtension('framework', [
            'test' => true,
            'router' => ['utf8' => true],
            'secret' => 'secret',
            'session' => ['storage_factory_id' => 'session.storage.factory.mock_file'],
            'form' => ['enabled' => true],
            'http_method_override' => false,
        ]);

        $container->loadFromExtension('security', [
            'firewalls' => ['main' => []],
        ]);

        $container->loadFromExtension('doctrine', [
            'dbal' => [
                'url' => 'sqlite:///%kernel.cache_dir%/app.db',
                'logging' => false,
                ...(version_compare((string) InstalledVersions::getVersion('doctrine/doctrine-bundle'), '3.0.0', '<') ? ['use_savepoints' => true] : []),
            ],
            'orm' => [
                'auto_mapping' => true,
                'controller_resolver' => ['auto_mapping' => false],
                'mappings' => [
                    'sortable_behavior' => [
                        'type' => 'attribute',
                        'dir' => '%kernel.project_dir%/Entity',
                        'prefix' => 'Runroom\SortableBehaviorBundle\Tests\App\Entity',
                        'is_bundle' => false,
                    ],
                ],
            ],
        ]);

        $container->loadFromExtension('twig', [
            'strict_variables' => '%kernel.debug%',
        ]);
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $newVersion = version_compare((string) InstalledVersions::getVersion('sonata-project/admin-bundle'), '4.39.0', '>=');
        if ($newVersion) {
            $routes->import($this->getProjectDir() . '/routing.yaml');
        } else {
            $routes->import($this->getProjectDir() . '/routing-legacy.yaml');
        }
    }

    private function getBaseDir(): string
    {
        return sys_get_temp_dir() . '/runroom-sortable-behavior-bundle/var';
    }
}
