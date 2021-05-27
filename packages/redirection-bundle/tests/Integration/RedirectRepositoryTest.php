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

use Runroom\RedirectionBundle\Entity\Redirect;
use Runroom\RedirectionBundle\Factory\RedirectFactory;
use Runroom\RedirectionBundle\Repository\RedirectRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class RedirectRepositoryTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    private RedirectRepository $repository;

    protected function setUp(): void
    {
        parent::bootKernel();

        $this->repository = static::$container->get(RedirectRepository::class);
    }

    /** @test */
    public function itReturnsRedirect(): void
    {
        RedirectFactory::createOne(['source' => '/redirect', 'publish' => true]);

        $redirect = $this->repository->findOneBy(['source' => '/redirect']);

        if (null !== $redirect) {
            self::assertSame('/redirect', (string) $redirect);
            self::assertNotNull($redirect->getId());
            self::assertSame('/redirect', $redirect->getSource());
            self::assertNotEmpty($redirect->getDestination());
            self::assertNotNull($redirect->getHttpCode());
            self::assertTrue($redirect->getPublish());
        } else {
            self::fail('not found redirect');
        }
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
        RedirectFactory::createOne([
            'source' => '/it-is-unpublish',
            'publish' => false,
        ]);

        $redirect = $this->repository->findRedirect('/it-is-unpublish');

        self::assertNull($redirect);
    }

    /** @test */
    public function itReturnsTheRedirect(): void
    {
        RedirectFactory::createOne([
            'source' => '/redirect',
            'destination' => '/target',
            'httpCode' => Redirect::PERMANENT,
            'publish' => true,
        ]);

        $redirect = $this->repository->findRedirect('/redirect');

        self::assertSame([
            'destination' => '/target',
            'httpCode' => (string) Redirect::PERMANENT,
        ], $redirect);
    }
}
