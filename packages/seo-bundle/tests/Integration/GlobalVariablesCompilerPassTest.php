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

namespace Runroom\SeoBundle\Tests\Integration;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Runroom\SeoBundle\DependencyInjection\Compiler\GlobalVariablesCompilerPass;
use Runroom\SeoBundle\DependencyInjection\RunroomSeoExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class GlobalVariablesCompilerPassTest extends AbstractCompilerPassTestCase
{
    public function testItAddsGlobalXDefaultLocaleToTwig(): void
    {
        $this->setDefinition('twig', new Definition());
        $this->setParameter(RunroomSeoExtension::XDEFAULT_LOCALE, 'es');

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall('twig', 'addGlobal', [
            'xDefaultLocale',
            'es',
        ]);
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new GlobalVariablesCompilerPass());
    }
}
