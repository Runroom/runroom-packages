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
use Runroom\CkeditorSonataMediaBundle\Tests\App\Entity\GalleryHasMedia;
use Runroom\CkeditorSonataMediaBundle\Tests\App\Entity\Media;
use Sonata\AdminBundle\SonataAdminBundle;
use Sonata\Doctrine\Bridge\Symfony\SonataDoctrineBundle;
use Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle;
use Sonata\MediaBundle\SonataMediaBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

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

    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader): void
    {
        $c->loadFromExtension('framework', [
            'test' => true,
            'router' => ['utf8' => true],
            'secret' => 'secret',
        ]);

        $c->loadFromExtension('security', [
            'firewalls' => ['main' => ['anonymous' => true]],
        ]);

        $c->loadFromExtension('doctrine', [
            'dbal' => ['url' => 'sqlite://:memory:', 'logging' => false],
            'orm' => ['auto_mapping' => true],
        ]);

        $c->loadFromExtension('twig', [
            'exception_controller' => null,
            'strict_variables' => '%kernel.debug%',
        ]);

        $c->loadFromExtension('sonata_media', [
            'default_context' => 'default',
            'contexts' => ['default' => []],
            'cdn' => null,
            'db_driver' => 'doctrine_orm',
            'class' => [
                'media' => Media::class,
                'gallery_has_media' => GalleryHasMedia::class,
                'gallery' => Gallery::class,
            ],
            'filesystem' => ['local' => null],
        ]);
    }

    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
    }

    private function getBaseDir(): string
    {
        return sys_get_temp_dir() . '/runroom-ckeditor-sonata-media-bundle/var';
    }
}
