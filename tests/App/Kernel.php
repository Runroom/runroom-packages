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

namespace Tests\App;

use A2lix\AutoFormBundle\A2lixAutoFormBundle;
use A2lix\TranslationFormBundle\A2lixTranslationFormBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Fidry\AliceDataFixtures\Bridge\Symfony\FidryAliceDataFixturesBundle;
use FOS\CKEditorBundle\FOSCKEditorBundle;
use Knp\Bundle\MenuBundle\KnpMenuBundle;
use Knp\DoctrineBehaviors\DoctrineBehaviorsBundle;
use Nelmio\Alice\Bridge\Symfony\NelmioAliceBundle;
use Runroom\BasicPageBundle\RunroomBasicPageBundle;
use Runroom\CkeditorSonataMediaBundle\RunroomCkeditorSonataMediaBundle;
use Runroom\CookiesBundle\RunroomCookiesBundle;
use Runroom\FormHandlerBundle\RunroomFormHandlerBundle;
use Runroom\RedirectionBundle\RunroomRedirectionBundle;
use Runroom\RedirectionBundle\Tests\App\Entity\Entity;
use Runroom\RedirectionBundle\Tests\App\Entity\WrongEntity;
use Runroom\RenderEventBundle\RunroomRenderEventBundle;
use Runroom\SeoBundle\RunroomSeoBundle;
use Runroom\SortableBehaviorBundle\RunroomSortableBehaviorBundle;
use Runroom\TranslationBundle\RunroomTranslationBundle;
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
use Tests\App\Entity\Gallery;
use Tests\App\Entity\GalleryHasMedia;
use Tests\App\Entity\Media;

final class Kernel extends BaseKernel
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
            new FOSCKEditorBundle(),
            new FrameworkBundle(),
            new KnpMenuBundle(),
            new NelmioAliceBundle(),
            new SecurityBundle(),
            new TwigBundle(),
            new SonataMediaBundle(),
            new SonataDoctrineBundle(),
            new SonataDoctrineORMAdminBundle(),
            new SonataAdminBundle(),

            new RunroomBasicPageBundle(),
            new RunroomCkeditorSonataMediaBundle(),
            new RunroomCookiesBundle(),
            new RunroomFormHandlerBundle(),
            new RunroomRedirectionBundle(),
            new RunroomRenderEventBundle(),
            new RunroomSeoBundle(),
            new RunroomSortableBehaviorBundle(),
            new RunroomTranslationBundle(),
        ];
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }

    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader): void
    {
        $loader->load($this->getProjectDir() . '/services.yaml');

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
                    'entity' => [
                        'type' => 'annotation',
                        'dir' => '%kernel.project_dir%/Entity',
                        'prefix' => 'Tests\App\Entity',
                        'is_bundle' => false,
                    ],
                    'redirection' => [
                        'type' => 'annotation',
                        'dir' => '%kernel.project_dir%/../../packages/redirection-bundle/tests/App/Entity',
                        'prefix' => 'Runroom\RedirectionBundle\Tests\App\Entity',
                        'is_bundle' => false,
                    ],
                    'sortable_behavior' => [
                        'type' => 'annotation',
                        'dir' => '%kernel.project_dir%/../../packages/sortable-behavior-bundle/tests/App/Entity',
                        'prefix' => 'Runroom\SortableBehaviorBundle\Tests\App\Entity',
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

        $c->loadFromExtension('runroom_cookies', [
            'cookies' => [
                'mandatory_cookies' => [[
                    'name' => 'test',
                    'cookies' => [['name' => 'test']],
                ]],
                'performance_cookies' => [[
                    'name' => 'test',
                    'cookies' => [['name' => 'test']],
                ]],
                'targeting_cookies' => [[
                    'name' => 'test',
                    'cookies' => [['name' => 'test']],
                ]],
            ],
        ]);

        $c->loadFromExtension('runroom_seo', [
            'locales' => ['es'],
            'xdefault_locale' => 'es',
            'class' => ['media' => Media::class],
        ]);

        $c->loadFromExtension('runroom_redirection', [
            'enable_automatic_redirections' => true,
            'automatic_redirections' => [
                Entity::class => [
                    'route' => 'route.entity',
                    'routeParameters' => ['slug' => 'slug'],
                ],
                WrongEntity::class => [
                    'route' => 'route.missing',
                    'routeParameters' => ['slug' => 'slug'],
                ],
            ],
        ]);
    }

    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
        $routes->import($this->getProjectDir() . '/routing.yaml');

        $routes->add('/entity/{slug}', 'controller', 'route.entity');
    }
}
