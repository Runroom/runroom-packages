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

namespace Runroom\CkeditorSonataMediaBundle\Tests\App;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Knp\Bundle\MenuBundle\KnpMenuBundle;
use Runroom\CkeditorSonataMediaBundle\RunroomCkeditorSonataMediaBundle;
use Runroom\CkeditorSonataMediaBundle\Tests\App\Entity\Gallery;
use Runroom\CkeditorSonataMediaBundle\Tests\App\Entity\GalleryItem;
use Runroom\CkeditorSonataMediaBundle\Tests\App\Entity\Media;
use Sonata\AdminBundle\SonataAdminBundle;
use Sonata\Doctrine\Bridge\Symfony\SonataDoctrineBundle;
use Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle;
use Sonata\MediaBundle\Model\GalleryItemInterface;
use Sonata\MediaBundle\SonataMediaBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Symfony\Component\Security\Http\Authentication\AuthenticatorManager;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        return [
            new DoctrineBundle(),
            new FrameworkBundle(),
            new KnpMenuBundle(),
            new SecurityBundle(),
            new SonataAdminBundle(),
            new SonataDoctrineBundle(),
            new SonataDoctrineORMAdminBundle(),
            new SonataMediaBundle(),
            new TwigBundle(),

            new RunroomCkeditorSonataMediaBundle(),
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

    /**
     * @todo: Simplify security configuration when dropping support for Symfony 4.4
     * @todo: Simplify media configuration when dropping support for Sonata 3
     */
    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->loadFromExtension('framework', [
            'test' => true,
            'router' => ['utf8' => true],
            'secret' => 'secret',
        ]);

        $securityConfig = [
            'firewalls' => ['main' => []],
        ];

        if (class_exists(AuthenticatorManager::class)) {
            $securityConfig['enable_authenticator_manager'] = true;
        } else {
            $securityConfig['firewalls']['main']['anonymous'] = true;
        }

        $container->loadFromExtension('security', $securityConfig);

        $container->loadFromExtension('doctrine', [
            'dbal' => ['url' => 'sqlite:///%kernel.cache_dir%/app.db', 'logging' => false],
            'orm' => ['auto_mapping' => true],
        ]);

        $container->loadFromExtension('twig', [
            'exception_controller' => null,
            'strict_variables' => '%kernel.debug%',
        ]);

        $galleryItemKey = interface_exists(GalleryItemInterface::class) ? 'gallery_item' : 'gallery_has_media';

        $container->loadFromExtension('sonata_media', [
            'default_context' => 'default',
            'contexts' => ['default' => []],
            'cdn' => null,
            'db_driver' => 'doctrine_orm',
            'class' => [
                'media' => Media::class,
                $galleryItemKey => GalleryItem::class,
                'gallery' => Gallery::class,
            ],
            'filesystem' => ['local' => null],
        ]);
    }

    /**
     * @todo: Simplify this method when dropping support for Symfony 4.4
     *
     * @param RouteCollectionBuilder|RoutingConfigurator $routes
     */
    protected function configureRoutes($routes): void
    {
    }

    private function getBaseDir(): string
    {
        return sys_get_temp_dir() . '/runroom-ckeditor-sonata-media-bundle/var';
    }
}
