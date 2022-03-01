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

namespace Runroom\Testing\Tests\App;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Fidry\AliceDataFixtures\Bridge\Symfony\FidryAliceDataFixturesBundle;
use Knp\Bundle\MenuBundle\KnpMenuBundle;
use Nelmio\Alice\Bridge\Symfony\NelmioAliceBundle;
use Sonata\AdminBundle\SonataAdminBundle;
use Sonata\AdminBundle\Twig\Extension\DeprecatedTextExtension;
use Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Security\Http\Authentication\AuthenticatorManager;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        return [
            new DoctrineBundle(),
            new FidryAliceDataFixturesBundle(),
            new FrameworkBundle(),
            new KnpMenuBundle(),
            new NelmioAliceBundle(),
            new SecurityBundle(),
            new SonataAdminBundle(),
            new SonataDoctrineORMAdminBundle(),
            new TwigBundle(),
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
        $loader->load($this->getProjectDir() . '/services.php');

        $container->loadFromExtension('framework', [
            'test' => true,
            'router' => ['utf8' => true],
            'secret' => 'secret',
            'annotations' => ['enabled' => true],
            'property_access' => null,
            'translator' => null,
            'form' => null,
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

        if (class_exists(DeprecatedTextExtension::class)) {
            $container->loadFromExtension('sonata_admin', [
                'options' => [
                    'legacy_twig_text_extension' => false,
                ],
            ]);
        }

        $container->loadFromExtension('doctrine', [
            'dbal' => ['url' => 'sqlite:///%kernel.cache_dir%/app.db', 'logging' => false],
            'orm' => [
                'auto_mapping' => true,
                'mappings' => [
                    'redirection' => [
                        'type' => 'annotation',
                        'dir' => '%kernel.project_dir%/Entity',
                        'prefix' => 'Runroom\Testing\Tests\App\Entity',
                        'is_bundle' => false,
                    ],
                ],
            ],
        ]);
    }

    /**
     * @todo: Add typehint when dropping support for Symfony 4
     *
     * @param RoutingConfigurator $routes
     */
    protected function configureRoutes($routes): void
    {
    }

    private function getBaseDir(): string
    {
        return sys_get_temp_dir() . '/runroom-testing/var';
    }
}
