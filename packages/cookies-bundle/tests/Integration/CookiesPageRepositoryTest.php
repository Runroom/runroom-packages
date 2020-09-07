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

namespace Runroom\CookiesBundle\Tests\Integration;

use Runroom\CookiesBundle\Repository\CookiesPageRepository;
use Runroom\Testing\TestCase\DoctrineTestCase;

class CookiesPageRepositoryTest extends DoctrineTestCase
{
    /** @var CookiesPageRepository */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = static::$container->get(CookiesPageRepository::class);
    }

    /** @test */
    public function ifFindsCookiesPageById(): void
    {
        $cookiesPage = $this->repository->find(1);

        if (null !== $cookiesPage) {
            self::assertSame(1, $cookiesPage->getId());
            self::assertSame('Cookies policy', $cookiesPage->__toString());
            self::assertNotNull($cookiesPage->getContent());
        } else {
            self::fail('not found cookiesPage');
        }
    }

    protected function getDataFixtures(): array
    {
        return ['cookies_page.yaml'];
    }
}
