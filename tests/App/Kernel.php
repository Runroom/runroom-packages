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

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Fidry\AliceDataFixtures\Bridge\Symfony\FidryAliceDataFixturesBundle;
use Knp\DoctrineBehaviors\DoctrineBehaviorsBundle;
use Nelmio\Alice\Bridge\Symfony\NelmioAliceBundle;
use Runroom\BasicPageBundle\RunroomBasicPageBundle;
use Runroom\BasicPageBundle\Tests\App\Media;
use Runroom\CkeditorSonataMediaBundle\RunroomCkeditorSonataMediaBundle;
use Runroom\FormHandlerBundle\RunroomFormHandlerBundle;
use Runroom\RedirectionBundle\RunroomRedirectionBundle;
use Runroom\RedirectionBundle\Tests\App\Entity\Entity;
use Runroom\RedirectionBundle\Tests\App\Entity\WrongEntity;
use Runroom\RenderEventBundle\RunroomRenderEventBundle;
use Runroom\SeoBundle\RunroomSeoBundle;
use Runroom\SortableBehaviorBundle\RunroomSortableBehaviorBundle;
use Runroom\TranslationBundle\RunroomTranslationBundle;
use Sonata\MediaBundle\SonataMediaBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
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
            new DoctrineBehaviorsBundle(),
            new DoctrineBundle(),
            new FidryAliceDataFixturesBundle(),
            new FrameworkBundle(),
            new NelmioAliceBundle(),
            new SecurityBundle(),
            new TwigBundle(),
            new SonataMediaBundle(),

            new RunroomBasicPageBundle(),
            new RunroomCkeditorSonataMediaBundle(),
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
        $container->loadFromExtension('framework', [
            'test' => true,
            'router' => ['utf8' => true],
            'secret' => 'secret',
            'session' => null,
        ]);

        $container->loadFromExtension('security', [
            'firewalls' => ['main' => ['anonymous' => true]],
        ]);

        $container->loadFromExtension('doctrine', [
            'dbal' => ['url' => 'sqlite://:memory:'],
            'orm' => [
                'auto_mapping' => true,
                'mappings' => [
                    'Entity' => [
                        'type' => 'annotation',
                        'dir' => '%kernel.project_dir%/../../packages/redirection-bundle/tests/App/Entity',
                        'prefix' => 'Runroom\RedirectionBundle\Tests\App\Entity',
                        'is_bundle' => false,
                    ],
                ],
            ],
        ]);

        $container->loadFromExtension('twig', [
            'exception_controller' => null,
            'strict_variables' => '%kernel.debug%',
        ]);

        $container->loadFromExtension('sonata_media', [
            'default_context' => 'default',
            'contexts' => ['default' => []],
            'cdn' => null,
            'filesystem' => ['local' => null],
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
