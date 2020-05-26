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

namespace Runroom\BasicPageBundle\Tests\Integration;

use Doctrine\ORM\NoResultException;
use Runroom\BasicPageBundle\Entity\BasicPage;
use Runroom\BasicPageBundle\Repository\BasicPageRepository;
use Runroom\BasicPageBundle\Tests\TestCase\DoctrineIntegrationTestBase;

class BasicPageRepositoryTest extends DoctrineIntegrationTestBase
{
    protected const STATIC_PAGE_ID = 1;

    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = static::$container->get(BasicPageRepository::class);
    }

    /**
     * @test
     */
    public function itFindsBasicPageGivenItsSlug()
    {
        $BasicPage = $this->repository->findBySlug('slug');

        $this->assertInstanceOf(BasicPage::class, $BasicPage);
        $this->assertSame(self::STATIC_PAGE_ID, $BasicPage->getId());
    }

    /**
     * @test
     */
    public function itDoesNotFindUnPublishedStatigPage()
    {
        $this->expectException(NoResultException::class);

        $this->repository->findBySlug('unpublished');
    }

    protected function getDataFixtures(): array
    {
        return ['basic_pages.yaml'];
    }
}
