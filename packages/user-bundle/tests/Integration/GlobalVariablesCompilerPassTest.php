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

namespace Sonata\AdminBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Runroom\UserBundle\DependencyInjection\Compiler\GlobalVariablesCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Twig\Environment;

final class GlobalVariablesCompilerPassTest extends AbstractCompilerPassTestCase
{
    /** @test */
    public function itAddsGlobalVariableToTwig(): void
    {
        $this->container->register('twig', Environment::class);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall('twig', 'addGlobal', ['runroom_user', new Reference('runroom_user.twig.global_variables')]);
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new GlobalVariablesCompilerPass());
    }
}
