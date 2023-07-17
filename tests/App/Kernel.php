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
use DAMA\DoctrineTestBundle\DAMADoctrineTestBundle;
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
use Runroom\UserBundle\Entity\User;
use Runroom\UserBundle\RunroomUserBundle;
use Sonata\AdminBundle\SonataAdminBundle;
use Sonata\BlockBundle\SonataBlockBundle;
use Sonata\Doctrine\Bridge\Symfony\SonataDoctrineBundle;
use Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle;
use Sonata\Form\Bridge\Symfony\SonataFormBundle;
use Sonata\MediaBundle\SonataMediaBundle;
use Sonata\Twig\Bridge\Symfony\SonataTwigBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use SymfonyCasts\Bundle\ResetPassword\SymfonyCastsResetPasswordBundle;
use Tests\App\Entity\Gallery;
use Tests\App\Entity\GalleryItem;
use Tests\App\Entity\Media;
use Zenstruck\Foundry\ZenstruckFoundryBundle;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        return [
            new A2lixAutoFormBundle(),
            new A2lixTranslationFormBundle(),
            new DAMADoctrineTestBundle(),
            new DoctrineBehaviorsBundle(),
            new DoctrineBundle(),
            new FidryAliceDataFixturesBundle(),
            new FOSCKEditorBundle(),
            new FrameworkBundle(),
            new KnpMenuBundle(),
            new NelmioAliceBundle(),
            new SecurityBundle(),
            new TwigBundle(),
            new SonataAdminBundle(),
            new SonataBlockBundle(),
            new SonataDoctrineBundle(),
            new SonataDoctrineORMAdminBundle(),
            new SonataMediaBundle(),
            new SonataFormBundle(),
            new SonataTwigBundle(),
            new SymfonyCastsResetPasswordBundle(),
            new ZenstruckFoundryBundle(),

            new RunroomBasicPageBundle(),
            new RunroomCkeditorSonataMediaBundle(),
            new RunroomCookiesBundle(),
            new RunroomFormHandlerBundle(),
            new RunroomRedirectionBundle(),
            new RunroomRenderEventBundle(),
            new RunroomSeoBundle(),
            new RunroomSortableBehaviorBundle(),
            new RunroomTranslationBundle(),
            new RunroomUserBundle(),
        ];
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $loader->load($this->getProjectDir() . '/services.php');

        $container->setParameter('kernel.default_locale', 'en');

        $container->loadFromExtension('framework', [
            'test' => true,
            'router' => ['utf8' => true],
            'session' => ['storage_factory_id' => 'session.storage.factory.mock_file'],
            'secret' => 'secret',
            'http_method_override' => false,
            'mailer' => [
                'enabled' => true,
                'dsn' => 'null://null',
            ],
        ]);

        $securityConfig = [
            'access_decision_manager' => ['strategy' => 'unanimous'],
            'access_control' => [
                ['path' => '^/dashboard$', 'role' => 'ROLE_USER'],
            ],
            'providers' => [
                'admin_user_provider' => [
                    'id' => 'runroom.user.provider.user',
                ],
            ],
            'password_hashers' => [User::class => ['algorithm' => 'plaintext']],
            'firewalls' => ['main' => [
                'lazy' => true,
                'pattern' => '/(.*)',
                'provider' => 'admin_user_provider',
                'context' => 'user',
                'custom_authenticator' => 'runroom.user.security.user_authenticator',
                'logout' => [
                    'path' => 'runroom_user_logout',
                    'target' => 'runroom_user_login',
                ],
                'remember_me' => [
                    'secret' => 'secret',
                    'lifetime' => 2_629_746,
                    'path' => '/',
                ],
            ]],
        ];

        // @todo: Remove if when dropping support of Symfony 5.4
        if (!class_exists(IsGranted::class)) {
            $securityConfig['enable_authenticator_manager'] = true;
        }

        $container->loadFromExtension('security', $securityConfig);

        $container->loadFromExtension('zenstruck_foundry', [
            'auto_refresh_proxies' => false,
        ]);

        $container->loadFromExtension('doctrine', [
            'dbal' => ['url' => 'sqlite:///%kernel.cache_dir%/app.db', 'logging' => false],
            'orm' => [
                'auto_mapping' => true,
                'mappings' => [
                    'entity' => [
                        'type' => 'attribute',
                        'dir' => '%kernel.project_dir%/Entity',
                        'prefix' => 'Tests\App\Entity',
                        'is_bundle' => false,
                    ],
                    'redirection' => [
                        'type' => 'attribute',
                        'dir' => '%kernel.project_dir%/../../packages/redirection-bundle/tests/App/Entity',
                        'prefix' => 'Runroom\RedirectionBundle\Tests\App\Entity',
                        'is_bundle' => false,
                    ],
                    'sortable_behavior' => [
                        'type' => 'attribute',
                        'dir' => '%kernel.project_dir%/../../packages/sortable-behavior-bundle/tests/App/Entity',
                        'prefix' => 'Runroom\SortableBehaviorBundle\Tests\App\Entity',
                        'is_bundle' => false,
                    ],
                    'testing' => [
                        'type' => 'attribute',
                        'dir' => '%kernel.project_dir%/../../packages/testing/tests/App/Entity',
                        'prefix' => 'Runroom\Testing\Tests\App\Entity',
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

        $container->loadFromExtension('symfonycasts_reset_password', [
            'request_password_repository' => 'symfonycasts.reset_password.fake_request_repository',
        ]);

        $container->loadFromExtension('sonata_block', [
            'http_cache' => false,
        ]);

        $container->loadFromExtension('sonata_media', [
            'default_context' => 'default',
            'contexts' => ['default' => []],
            'cdn' => null,
            'db_driver' => 'doctrine_orm',
            'class' => [
                'media' => Media::class,
                'gallery_item' => GalleryItem::class,
                'gallery' => Gallery::class,
            ],
            'filesystem' => ['local' => [
                'directory' => '%kernel.project_dir%/uploads',
                'create' => true,
            ]],
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

        $container->loadFromExtension('runroom_user', [
            'reset_password' => ['enabled' => true],
        ]);
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import($this->getProjectDir() . '/routing.yaml');

        $routes->add('route.entity', '/entity/{slug}')
            ->controller('controller');
    }
}
