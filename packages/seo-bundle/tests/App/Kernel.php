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

namespace Runroom\SeoBundle\Tests\App;

use A2lix\AutoFormBundle\A2lixAutoFormBundle;
use A2lix\TranslationFormBundle\A2lixTranslationFormBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Fidry\AliceDataFixtures\Bridge\Symfony\FidryAliceDataFixturesBundle;
use Knp\Bundle\MenuBundle\KnpMenuBundle;
use Knp\DoctrineBehaviors\DoctrineBehaviorsBundle;
use Nelmio\Alice\Bridge\Symfony\NelmioAliceBundle;
use Runroom\RenderEventBundle\RunroomRenderEventBundle;
use Runroom\SeoBundle\RunroomSeoBundle;
use Runroom\SeoBundle\Tests\App\Entity\Gallery;
use Runroom\SeoBundle\Tests\App\Entity\GalleryHasMedia;
use Runroom\SeoBundle\Tests\App\Entity\Media;
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
            new A2lixAutoFormBundle(),
            new A2lixTranslationFormBundle(),
            new DoctrineBehaviorsBundle(),
            new DoctrineBundle(),
            new FidryAliceDataFixturesBundle(),
            new FrameworkBundle(),
            new KnpMenuBundle(),
            new NelmioAliceBundle(),
            new SecurityBundle(),
            new SonataAdminBundle(),
            new SonataMediaBundle(),
            new SonataDoctrineBundle(),
            new SonataDoctrineORMAdminBundle(),
            new TwigBundle(),

            new RunroomRenderEventBundle(),
            new RunroomSeoBundle(),
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
        $c->setParameter('kernel.default_locale', 'en');

        $c->loadFromExtension('framework', [
            'test' => true,
            'router' => ['utf8' => true],
            'secret' => 'secret',
            'session' => ['storage_id' => 'session.storage.mock_file'],
        ]);

        $c->loadFromExtension('security', [
            'firewalls' => ['main' => ['anonymous' => true]],
        ]);

        $c->loadFromExtension('doctrine', [
            'dbal' => ['url' => 'sqlite://:memory:', 'logging' => false],
            'orm' => [
                'auto_mapping' => true,
                'mappings' => [
                    'redirection' => [
                        'type' => 'annotation',
                        'dir' => '%kernel.project_dir%/Entity',
                        'prefix' => 'Runroom\SeoBundle\Tests\App\Entity',
                        'is_bundle' => false,
                    ],
                ],
            ],
        ]);

        $c->loadFromExtension('twig', [
            'exception_controller' => null,
            'strict_variables' => '%kernel.debug%',
        ]);

        $c->loadFromExtension('a2lix_translation_form', [
            'locales' => ['es', 'en', 'ca'],
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

        $c->loadFromExtension('runroom_seo', [
            'locales' => ['es', 'en', 'ca'],
            'xdefault_locale' => 'es',
            'class' => ['media' => Media::class],
        ]);
    }

    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
    }

    private function getBaseDir(): string
    {
        return sys_get_temp_dir() . '/runroom-seo-bundle/var';
    }
}
