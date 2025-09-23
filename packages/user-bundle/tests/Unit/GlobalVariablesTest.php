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
use Runroom\UserBundle\Twig\GlobalVariables;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\Pool;
use Symfony\Component\DependencyInjection\Container;

final class GlobalVariablesTest extends TestCase
{
    public function testItGetsAdminByClass(): void
    {
        $container = new Container();
        $container->set('runroom.user.admin.user', static::createStub(AdminInterface::class));

        $pool = new Pool($container, ['runroom.user.admin.user']);
        $globalVariables = new GlobalVariables($pool, false);

        $admin = $globalVariables->getUserAdmin();

        static::assertInstanceOf(AdminInterface::class, $admin);
    }

    public function testItHasRequestPasswordEnabled(): void
    {
        $container = new Container();
        $container->set('runroom.user.admin.user', static::createStub(AdminInterface::class));

        $pool = new Pool($container, ['runroom.user.admin.user']);

        $globalVariables = new GlobalVariables($pool, true);

        static::assertTrue($globalVariables->getHasRequestPasswordEnabled());
    }
}
