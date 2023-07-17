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
use Runroom\Testing\Tests\App\Entity\Test;

final class DoctrineTestCaseTest extends DoctrineTestCase
{
    public function testItDoesInitializeTheDatabase(): void
    {
        static::assertNotNull(self::$entityManager->find(Test::class, 1));
    }

    protected function getDataFixtures(): array
    {
        return ['test.yaml'];
    }
}
