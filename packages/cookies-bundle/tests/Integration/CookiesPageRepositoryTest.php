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

use Runroom\CookiesBundle\Entity\CookiesPage;
use Runroom\CookiesBundle\Repository\CookiesPageRepository;
use Runroom\CookiesBundle\Tests\TestCase\DoctrineIntegrationTestBase;

class CookiesPageRepositoryTest extends DoctrineIntegrationTestBase
{
    /** @var CookiesPageRepository */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = static::$container->get(CookiesPageRepository::class);
    }

    /**
     * @test
     */
    public function itFindsCookiesPage(): void
    {
        $cookies = $this->repository->findCookiesPage();
        $this->assertInstanceOf(CookiesPage::class, $cookies);
    }

    protected function getDataFixtures(): array
    {
        return ['cookies_page.yaml'];
    }
}
