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
// use Sonata\Doctrine\Mapper\Builder\OptionsBuilder;
// use Sonata\Doctrine\Mapper\DoctrineCollector;
use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class RunroomSeoExtension extends Extension
{
    public const XDEFAULT_LOCALE = 'runroom_seo.xdefault_locale';

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
        $loader->load('admin.yaml');

        $container->registerForAutoconfiguration(AlternateLinksProviderInterface::class)
            ->addTag('runroom.seo.alternate_links');

        $container->registerForAutoconfiguration(MetaInformationProviderInterface::class)
            ->addTag('runroom.seo.meta_information');

        $container->getDefinition(AlternateLinksBuilder::class)
            ->setArgument(1, $config['locales']);

        $container->setParameter(self::XDEFAULT_LOCALE, $config['xdefault_locale']);

        $this->mapMediaField('image', MetaInformation::class, $config);
    }

    protected function mapMediaField(string $fieldName, string $entityName, array $config): void
    {
        // $options = OptionsBuilder::create()
        //     ->add('fieldName', $fieldName)
        //     ->add('targetEntity', $config['class']['media'])
        //     ->add('cascade', ['all'])
        //     ->add('mappedBy', null)
        //     ->add('inversedBy', null)
        //     ->add('joinColumns', [['referencedColumnName' => 'id']])
        //     ->add('orphanRemoval', false);
        $options = [
            'fieldName' => $fieldName,
            'targetEntity' => $config['class']['media'],
            'cascade' => ['all'],
            'mappedBy' => null,
            'inversedBy' => null,
            'joinColumns' => [['referencedColumnName' => 'id']],
            'orphanRemoval' => false,
        ];

        DoctrineCollector::getInstance()->addAssociation($entityName, 'mapManyToOne', $options);
    }
}
