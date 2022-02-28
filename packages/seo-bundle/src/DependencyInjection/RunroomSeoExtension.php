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

namespace Runroom\SeoBundle\DependencyInjection;

use Runroom\SeoBundle\AlternateLinks\AlternateLinksBuilder;
use Runroom\SeoBundle\AlternateLinks\AlternateLinksProviderInterface;
use Runroom\SeoBundle\Entity\MetaInformation;
use Runroom\SeoBundle\MetaInformation\MetaInformationProviderInterface;
use Sonata\Doctrine\Mapper\Builder\OptionsBuilder;
use Sonata\Doctrine\Mapper\DoctrineCollector;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @phpstan-type SeoBundleConfiguration = array{
 *     locales: string[],
 *     xdefault_locale: string,
 *     class: array{ media: class-string },
 * }
 */
final class RunroomSeoExtension extends Extension
{
    public const XDEFAULT_LOCALE = 'runroom_seo.xdefault_locale';

    public function load(array $configs, ContainerBuilder $container): void
    {
        $bundles = $container->getParameter('kernel.bundles');
        \assert(\is_array($bundles));

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.php');

        if (isset($bundles['SonataAdminBundle'], $bundles['A2lixTranslationFormBundle'])) {
            $loader->load('admin.php');
        }

        $configuration = new Configuration();
        /** @phpstan-var SeoBundleConfiguration */
        $config = $this->processConfiguration($configuration, $configs);

        $container->registerForAutoconfiguration(AlternateLinksProviderInterface::class)
            ->addTag('runroom.seo.alternate_links');

        $container->registerForAutoconfiguration(MetaInformationProviderInterface::class)
            ->addTag('runroom.seo.meta_information');

        $container->getDefinition(AlternateLinksBuilder::class)
            ->setArgument(1, $config['locales']);

        $container->setParameter(self::XDEFAULT_LOCALE, $config['xdefault_locale']);

        $this->mapMediaField('image', MetaInformation::class, $config);
    }

    /**
     * @phpstan-param class-string $entityName
     * @phpstan-param SeoBundleConfiguration $config
     */
    protected function mapMediaField(string $fieldName, string $entityName, array $config): void
    {
        $options = OptionsBuilder::createManyToOne($fieldName, $config['class']['media'])
            ->cascade(['all'])
            ->addJoin([
                'name' => 'image_id',
                'referencedColumnName' => 'id',
            ]);

        DoctrineCollector::getInstance()->addAssociation($entityName, 'mapManyToOne', $options);
    }
}
