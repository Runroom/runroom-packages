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

namespace Runroom\UserBundle\Tests\App;

use DAMA\DoctrineTestBundle\DAMADoctrineTestBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Knp\Bundle\MenuBundle\KnpMenuBundle;
use Runroom\UserBundle\Entity\User;
use Runroom\UserBundle\RunroomUserBundle;
use Sonata\AdminBundle\SonataAdminBundle;
use Sonata\BlockBundle\SonataBlockBundle;
use Sonata\Doctrine\Bridge\Symfony\SonataDoctrineBundle;
use Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle;
use Sonata\Form\Bridge\Symfony\SonataFormBundle;
use Sonata\Twig\Bridge\Symfony\SonataTwigBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\UX\StimulusBundle\StimulusBundle;
use SymfonyCasts\Bundle\ResetPassword\SymfonyCastsResetPasswordBundle;
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
            new SonataBlockBundle(),
            new SonataDoctrineBundle(),
            new SonataDoctrineORMAdminBundle(),
            new SonataFormBundle(),
            new SonataTwigBundle(),
            new TwigBundle(),
            new ZenstruckFoundryBundle(),
            new SymfonyCastsResetPasswordBundle(),
            new StimulusBundle(),

            new RunroomUserBundle(),
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
        $container->loadFromExtension('framework', [
            'annotations' => false,
            'test' => true,
            'router' => ['utf8' => true],
            'secret' => 'secret',
            'session' => ['storage_factory_id' => 'session.storage.factory.mock_file'],
            'http_method_override' => false,
            'assets' => ['enabled' => true],
            'mailer' => [
                'enabled' => true,
                'dsn' => 'null://null',
            ],
        ]);

        $container->loadFromExtension('security', [
            'password_hashers' => [User::class => ['algorithm' => 'plaintext']],
            'access_decision_manager' => ['strategy' => 'unanimous'],
            'access_control' => [
                ['path' => '^/dashboard$', 'role' => 'ROLE_USER'],
            ],
            'providers' => [
                'admin_user_provider' => [
                    'id' => 'runroom.user.provider.user',
                ],
            ],
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
        ]);

        $container->loadFromExtension('doctrine', [
            'dbal' => [
                'url' => 'sqlite:///%kernel.cache_dir%/app.db',
                'logging' => false,
                'use_savepoints' => true,
            ],
            'orm' => [
                'auto_mapping' => true,
                'controller_resolver' => ['auto_mapping' => false],
            ],
        ]);

        $container->loadFromExtension('twig', [
            'exception_controller' => null,
            'strict_variables' => '%kernel.debug%',
        ]);

        $container->loadFromExtension('symfonycasts_reset_password', [
            'request_password_repository' => 'symfonycasts.reset_password.fake_request_repository',
        ]);

        $container->loadFromExtension('runroom_user', [
            'reset_password' => ['enabled' => true],
        ]);
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import($this->getProjectDir() . '/routing.yaml');
    }

    private function getBaseDir(): string
    {
        return sys_get_temp_dir() . '/runroom-user-bundle/var';
    }
}
