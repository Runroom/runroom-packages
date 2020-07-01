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

namespace Runroom\Testing\Tests\Integration;

use Runroom\Testing\TestCase\DoctrineTestCase;

class DoctrineTestCaseTest extends DoctrineTestCase
{
    /** @test */
    public function itDoesInitializeTheDatabase(): void
    {
        self::assertNotNull(static::$kernel);
    }

    /** @test */
    public function itThrowsExceptionWhenKernelClassIsNotSet(): void
    {
        $this->expectException(\LogicException::class);

        unset($_SERVER['KERNEL_CLASS'], $_ENV['KERNEL_CLASS']);

        $this->createKernel();
    }

    /** @test */
    public function itThrowsExceptionWhenKernelClassDoesNotExist(): void
    {
        $this->expectException(\RuntimeException::class);

        $_SERVER['KERNEL_CLASS'] = $_ENV['KERNEL_CLASS'] = 'App\DoesNotExist\Kernel';

        $this->createKernel();
    }

    protected function getDataFixtures(): array
    {
        return [];
    }
}
