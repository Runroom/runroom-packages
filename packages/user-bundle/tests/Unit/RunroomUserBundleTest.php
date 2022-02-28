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

namespace Runroom\UserBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Runroom\UserBundle\DependencyInjection\Compiler\GlobalVariablesCompilerPass;
use Runroom\UserBundle\RunroomUserBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RunroomUserBundleTest extends TestCase
{
    /**
     * @test
     */
    public function itBuilds(): void
    {
        $containerBuilder = $this->createMock(ContainerBuilder::class);
        $containerBuilder->expects(static::once())->method('addCompilerPass')
            ->with(new GlobalVariablesCompilerPass());

        $bundle = new RunroomUserBundle();
        $bundle->build($containerBuilder);
    }
}
