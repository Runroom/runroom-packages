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
use Runroom\BasicPageBundle\Repository\BasicPageRepository;
use Runroom\Testing\TestCase\DoctrineTestCase;

class BasicPageRepositoryTest extends DoctrineTestCase
{
    /** @var BasicPageRepository */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = static::$container->get(BasicPageRepository::class);
    }

    /** @test */
    public function itFindsBasicPageGivenItsSlug(): void
    {
        $basicPage = $this->repository->findBySlug('slug');

        self::assertSame(1, $basicPage->getId());
    }

    /** @test */
    public function itDoesNotFindUnPublishedStatigPage(): void
    {
        $this->expectException(NoResultException::class);

        $this->repository->findBySlug('unpublished');
    }

    protected function getDataFixtures(): array
    {
        return ['basic_pages.yaml'];
    }
}
