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

namespace Runroom\CookiesBundle\Tests\App;

use A2lix\AutoFormBundle\A2lixAutoFormBundle;
use A2lix\TranslationFormBundle\A2lixTranslationFormBundle;
use DAMA\DoctrineTestBundle\DAMADoctrineTestBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use FOS\CKEditorBundle\FOSCKEditorBundle;
use Knp\Bundle\MenuBundle\KnpMenuBundle;
use Knp\DoctrineBehaviors\DoctrineBehaviorsBundle;
use Runroom\CookiesBundle\RunroomCookiesBundle;
use Sonata\AdminBundle\SonataAdminBundle;
use Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
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
            new KnpMenuBundle(),
            new FOSCKEditorBundle(),
            new FrameworkBundle(),
            new SecurityBundle(),
            new SonataAdminBundle(),
            new SonataDoctrineORMAdminBundle(),
            new TwigBundle(),
            new ZenstruckFoundryBundle(),

            new RunroomCookiesBundle(),
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
        $container->setParameter('kernel.default_locale', 'en');

        $container->loadFromExtension('framework', [
            'annotations' => false,
            'test' => true,
            'router' => ['utf8' => true],
            'secret' => 'secret',
            'session' => ['storage_factory_id' => 'session.storage.factory.mock_file'],
            'http_method_override' => false,
        ]);

        $securityConfig = [
            'firewalls' => ['main' => []],
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
            'dbal' => [
                'url' => 'sqlite:///%kernel.cache_dir%/app.db',
                'logging' => false,
                'use_savepoints' => true,
            ],
            'orm' => ['auto_mapping' => true],
        ]);

        $container->loadFromExtension('twig', [
            'exception_controller' => null,
            'strict_variables' => '%kernel.debug%',
        ]);

        $container->loadFromExtension('a2lix_translation_form', [
            'locales' => ['es', 'en', 'ca'],
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
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import($this->getProjectDir() . '/routing.yaml');
    }

    private function getBaseDir(): string
    {
        return sys_get_temp_dir() . '/runroom-cookies-bundle/var';
    }
}
