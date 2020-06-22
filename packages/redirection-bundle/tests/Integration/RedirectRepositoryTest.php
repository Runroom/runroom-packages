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

namespace Runroom\RedirectionBundle\Tests\Integration;

use Runroom\RedirectionBundle\Repository\RedirectRepository;
use Runroom\RedirectionBundle\Tests\TestCase\DoctrineIntegrationTestBase;

final class RedirectRepositoryTest extends DoctrineIntegrationTestBase
{
    /** @var RedirectRepository */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = static::$container->get(RedirectRepository::class);
    }

    /** @test */
    public function itReturnsRedirect(): void
    {
        $redirect = $this->repository->findOneBy(['source' => '/redirect']);

        self::assertNotNull($redirect->getId());
        self::assertSame('/redirect', $redirect->__toString());
        self::assertSame('/redirect', $redirect->getSource());
        self::assertSame('/target', $redirect->getDestination());
        self::assertSame(301, $redirect->getHttpCode());
        self::assertTrue($redirect->getPublish());
    }

    /** @test */
    public function itReturnsNullIfItDoesNotFindARedirect(): void
    {
        $redirect = $this->repository->findRedirect('/it-is-not-there');

        self::assertNull($redirect);
    }

    /** @test */
    public function itReturnsNullIfTheRedirectIsUnpublish(): void
    {
        $redirect = $this->repository->findRedirect('/it-is-unpublish');

        self::assertNull($redirect);
    }

    /** @test */
    public function itReturnsTheRedirect(): void
    {
        $redirect = $this->repository->findRedirect('/redirect');

        self::assertSame([
            'destination' => '/target',
            'httpCode' => '301',
        ], $redirect);
    }

    protected function getDataFixtures(): array
    {
        return ['redirects.yaml'];
    }
}
