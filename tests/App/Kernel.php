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
use Runroom\UserBundle\Entity\User;
use Runroom\UserBundle\RunroomUserBundle;
use Sonata\AdminBundle\SonataAdminBundle;
use Sonata\AdminBundle\Twig\Extension\DeprecatedTextExtension;
use Sonata\BlockBundle\SonataBlockBundle;
use Sonata\Doctrine\Bridge\Symfony\SonataDoctrineBundle;
use Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle;
use Sonata\MediaBundle\Model\GalleryItemInterface;
use Sonata\MediaBundle\SonataMediaBundle;
use Sonata\Twig\Bridge\Symfony\SonataTwigBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorageFactory;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Symfony\Component\Security\Http\Authentication\AuthenticatorManager;
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

    /**
     * @todo: Simplify security configuration when dropping support for Symfony 4
     * @todo: Simplify media configuration when dropping support for Sonata 3
     */
    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $loader->load($this->getProjectDir() . '/services.yaml');

        $container->setParameter('kernel.default_locale', 'en');

        $frameworkConfig = [
            'test' => true,
            'router' => ['utf8' => true],
            'secret' => 'secret',
            'mailer' => ['enabled' => true],
        ];

        if (class_exists(NativeSessionStorageFactory::class)) {
            $frameworkConfig['session'] = ['storage_factory_id' => 'session.storage.factory.mock_file'];
        } else {
            $frameworkConfig['session'] = ['storage_id' => 'session.storage.mock_file'];
        }

        $container->loadFromExtension('framework', $frameworkConfig);

        $securityConfig = [
            'access_decision_manager' => ['strategy' => 'unanimous'],
            'providers' => [
                'admin_user_provider' => [
                    'id' => 'runroom_user.provider.user',
                ],
            ],
            'firewalls' => ['main' => [
                'pattern' => '/(.*)',
                'provider' => 'admin_user_provider',
                'context' => 'user',
                'logout' => [
                    'path' => 'runroom_user_logout',
                    'target' => 'runroom_user_login',
                ],
                'remember_me' => [
                    'secret' => 'secret',
                    'lifetime' => 2629746,
                    'path' => '/',
                ],
            ]],
        ];

        if (class_exists(AuthenticatorManager::class)) {
            $securityConfig['enable_authenticator_manager'] = true;
            $securityConfig['firewalls']['main']['custom_authenticator'] = 'runroom_user.security.user_authenticator';
            $securityConfig['firewalls']['main']['lazy'] = true;
            $securityConfig['password_hashers'] = [User::class => ['algorithm' => 'auto']];
        } else {
            $securityConfig['firewalls']['main']['anonymous'] = true;
            $securityConfig['firewalls']['main']['form_login'] = [
                'login_path' => 'runroom_user_login',
                'check_path' => 'runroom_user_login',
                'default_target_path' => 'sonata_admin_dashboard',
            ];

            $securityConfig['encoders'] = [User::class => 'auto'];
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
                    'testing' => [
                        'type' => 'annotation',
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

        if (class_exists(DeprecatedTextExtension::class)) {
            $container->loadFromExtension('sonata_admin', [
                'options' => [
                    'legacy_twig_text_extension' => false,
                ],
            ]);
        }

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

    /**
     * @todo: Simplify this method when dropping support for Symfony 4
     *
     * @param RouteCollectionBuilder|RoutingConfigurator $routes
     */
    protected function configureRoutes($routes): void
    {
        if ($routes instanceof RoutingConfigurator) {
            $routes->import($this->getProjectDir() . '/routing.yaml');

            $routes->add('route.entity', '/entity/{slug}')
                ->controller('controller');

            return;
        }

        $routes->import($this->getProjectDir() . '/routing.yaml');

        $routes->add('/entity/{slug}', 'controller', 'route.entity');
    }
}
