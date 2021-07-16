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

namespace Runroom\BasicPageBundle\Tests\App;

use A2lix\AutoFormBundle\A2lixAutoFormBundle;
use A2lix\TranslationFormBundle\A2lixTranslationFormBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use FOS\CKEditorBundle\FOSCKEditorBundle;
use Knp\Bundle\MenuBundle\KnpMenuBundle;
use Knp\DoctrineBehaviors\DoctrineBehaviorsBundle;
use Runroom\BasicPageBundle\RunroomBasicPageBundle;
use Runroom\BasicPageBundle\Tests\App\Entity\Media;
use Runroom\RenderEventBundle\RunroomRenderEventBundle;
use Runroom\SeoBundle\RunroomSeoBundle;
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
use Symfony\Component\Routing\RouteCollectionBuilder;
use Symfony\Component\Security\Http\Authentication\AuthenticatorManager;
use Zenstruck\Foundry\ZenstruckFoundryBundle;

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
            new FOSCKEditorBundle(),
            new FrameworkBundle(),
            new KnpMenuBundle(),
            new SecurityBundle(),
            new SonataAdminBundle(),
            new SonataDoctrineORMAdminBundle(),
            new TwigBundle(),
            new ZenstruckFoundryBundle(),

            new RunroomBasicPageBundle(),
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

    /** @todo: Simplify security configuration when dropping support for Symfony 4.4 */
    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->setParameter('kernel.default_locale', 'en');

        $container->loadFromExtension('framework', [
            'test' => true,
            'router' => ['utf8' => true],
            'secret' => 'secret',
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

        $container->loadFromExtension('zenstruck_foundry', [
            'auto_refresh_proxies' => false,
        ]);

        $container->loadFromExtension('doctrine', [
            'dbal' => ['url' => 'sqlite:///%kernel.cache_dir%/app.db', 'logging' => false],
            'orm' => ['auto_mapping' => true],
        ]);

        $container->loadFromExtension('twig', [
            'exception_controller' => null,
            'strict_variables' => '%kernel.debug%',
        ]);

        $container->loadFromExtension('a2lix_translation_form', [
            'locales' => ['es', 'en', 'ca'],
        ]);

        $container->loadFromExtension('runroom_seo', [
            'locales' => ['es', 'en', 'ca'],
            'xdefault_locale' => 'es',
            'class' => ['media' => Media::class],
        ]);
    }

    /**
     * @todo: Simplify this method when dropping support for Symfony 4.4
     *
     * @param RouteCollectionBuilder|RoutingConfigurator $routes
     */
    protected function configureRoutes($routes): void
    {
        $routes->import($this->getProjectDir() . '/routing.yaml');
    }

    private function getBaseDir(): string
    {
        return sys_get_temp_dir() . '/runroom-basic-page-bundle/var';
    }
}
