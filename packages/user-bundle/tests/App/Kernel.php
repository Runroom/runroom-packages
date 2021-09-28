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

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Knp\Bundle\MenuBundle\KnpMenuBundle;
use Runroom\UserBundle\RunroomUserBundle;
use Sonata\AdminBundle\SonataAdminBundle;
use Sonata\AdminBundle\Twig\Extension\DeprecatedTextExtension;
use Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle;
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
use Zenstruck\Foundry\ZenstruckFoundryBundle;

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
            new SonataDoctrineORMAdminBundle(),
            new TwigBundle(),
            new ZenstruckFoundryBundle(),
            new SymfonyCastsResetPasswordBundle(),

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

    /** @todo: Simplify security configuration when dropping support for Symfony 4 */
    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
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

        $container->loadFromExtension('zenstruck_foundry', [
            'auto_refresh_proxies' => false,
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
    }

    private function getBaseDir(): string
    {
        return sys_get_temp_dir() . '/runroom-user-bundle/var';
    }
}
