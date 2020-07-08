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

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $loader->load($this->getProjectDir() . '/services.yaml');

        $container->setParameter('kernel.default_locale', 'en');

        $container->loadFromExtension('framework', [
            'test' => true,
            'router' => ['utf8' => true],
            'secret' => 'secret',
            'session' => ['storage_id' => 'session.storage.mock_file'],
        ]);

        $container->loadFromExtension('security', [
            'firewalls' => ['main' => ['anonymous' => true]],
        ]);

        $container->loadFromExtension('doctrine', [
            'dbal' => ['url' => 'sqlite://:memory:', 'logging' => false],
            'orm' => [
                'auto_mapping' => true,
                'mappings' => [
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

        $container->loadFromExtension('twig', [
            'exception_controller' => null,
            'strict_variables' => '%kernel.debug%',
        ]);

        $container->loadFromExtension('a2lix_translation_form', [
            'locales' => ['es', 'en', 'ca'],
        ]);

        $container->loadFromExtension('sonata_media', [
            'default_context' => 'default',
            'contexts' => ['default' => []],
            'cdn' => null,
            'db_driver' => 'doctrine_orm',
            'class' => ['media' => Media::class],
            'filesystem' => ['local' => null],
        ]);

        $container->loadFromExtension('runroom_cookies', [
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

        $container->loadFromExtension('runroom_seo', [
            'locales' => ['es'],
            'xdefault_locale' => 'es',
            'class' => ['media' => Media::class],
        ]);

        $container->loadFromExtension('runroom_redirection', [
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
        $routes->add('/entity/{slug}', 'controller', 'route.entity');
    }
}
