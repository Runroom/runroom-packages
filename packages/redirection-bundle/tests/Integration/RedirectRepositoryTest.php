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

        $this->assertSame('/redirect', $redirect->__toString());
        $this->assertSame('/redirect', $redirect->getSource());
        $this->assertSame('/target', $redirect->getDestination());
        $this->assertSame(301, $redirect->getHttpCode());
        $this->assertTrue($redirect->getPublish());
    }

    /** @test */
    public function itReturnsNullIfItDoesNotFindARedirect(): void
    {
        $redirect = $this->repository->findRedirect('/it-is-not-there');

        $this->assertNull($redirect);
    }

    /** @test */
    public function itReturnsNullIfTheRedirectIsUnpublish(): void
    {
        $redirect = $this->repository->findRedirect('/it-is-unpublish');

        $this->assertNull($redirect);
    }

    /** @test */
    public function itReturnsTheRedirect(): void
    {
        $redirect = $this->repository->findRedirect('/redirect');

        $this->assertSame([
            'destination' => '/target',
            'httpCode' => '301',
        ], $redirect);
    }

    protected function getDataFixtures(): array
    {
        return ['redirects.yaml'];
    }
}
