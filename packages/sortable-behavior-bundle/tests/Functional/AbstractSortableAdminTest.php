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

namespace Runroom\SortableBehaviorBundle\Tests\Functional;

use Runroom\SortableBehaviorBundle\Tests\App\Entity\SortableEntity;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use function Zenstruck\Foundry\anonymous;

use Zenstruck\Foundry\AnonymousFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class AbstractSortableAdminTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    /**
     * @test
     */
    public function itUpdatesPosition(): void
    {
        $client = static::createClient();

        /**
         * @psalm-suppress DeprecatedClass
         *
         * @todo: simplify when dropping support for ZenstruckFoundryBundle < 1.10
         */
        if (\function_exists('anonymous')) {
            $factory = anonymous(SortableEntity::class);
        } else {
            /**
             * @psalm-suppress InvalidArgument
             */
            $factory = AnonymousFactory::new(SortableEntity::class);
        }

        /**
         * @psalm-suppress PossiblyUndefinedArrayOffset
         */
        [$sortableEntity1, $sortableEntity2, $sortableEntity3, $sortableEntity4] = $factory->many(4)->create();

        /** @var Proxy<SortableEntity> $sortableEntity1 */
        static::assertSame(0, $sortableEntity1->getPosition());
        /** @var Proxy<SortableEntity> $sortableEntity2 */
        static::assertSame(1, $sortableEntity2->getPosition());
        /** @var Proxy<SortableEntity> $sortableEntity3 */
        static::assertSame(2, $sortableEntity3->getPosition());
        /** @var Proxy<SortableEntity> $sortableEntity4 */
        static::assertSame(3, $sortableEntity4->getPosition());

        $client->request('GET', '/tests/app/sortableentity/' . $sortableEntity1->getId() . '/move/down');
        $client->request('GET', '/tests/app/sortableentity/' . $sortableEntity2->getId() . '/move/bottom');
        $client->request('GET', '/tests/app/sortableentity/' . $sortableEntity3->getId() . '/move/up');
        $client->request('GET', '/tests/app/sortableentity/' . $sortableEntity4->getId() . '/move/top');

        $sortableEntity1->refresh();
        $sortableEntity2->refresh();
        $sortableEntity3->refresh();
        $sortableEntity4->refresh();

        static::assertSame(2, $sortableEntity1->getPosition());
        static::assertSame(3, $sortableEntity2->getPosition());
        static::assertSame(1, $sortableEntity3->getPosition());
        static::assertSame(0, $sortableEntity4->getPosition());
    }
}
