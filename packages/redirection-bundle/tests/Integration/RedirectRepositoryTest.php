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

final class RedirectRepositoryTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    private RedirectRepository $repository;

    protected function setUp(): void
    {
        $this->repository = static::getContainer()->get(RedirectRepository::class);
    }

    public function testItReturnsRedirect(): void
    {
        RedirectFactory::createOne(['source' => '/redirect', 'publish' => true]);

        $redirect = $this->repository->findOneBy(['source' => '/redirect']);

        if (null !== $redirect) {
            static::assertSame('/redirect', (string) $redirect);
            static::assertNotNull($redirect->getId());
            static::assertSame('/redirect', $redirect->getSource());
            static::assertNotEmpty($redirect->getDestination());
            static::assertNotNull($redirect->getHttpCode());
            static::assertTrue($redirect->getPublish());
        } else {
            static::fail('not found redirect');
        }
    }

    public function testItReturnsNullIfItDoesNotFindARedirect(): void
    {
        $redirect = $this->repository->findRedirect('/it-is-not-there');

        static::assertNull($redirect);
    }

    public function testItReturnsNullIfTheRedirectIsUnpublish(): void
    {
        RedirectFactory::createOne([
            'source' => '/it-is-unpublish',
            'publish' => false,
        ]);

        $redirect = $this->repository->findRedirect('/it-is-unpublish');

        static::assertNull($redirect);
    }

    public function testItReturnsTheRedirect(): void
    {
        RedirectFactory::createOne([
            'source' => '/redirect',
            'destination' => '/target',
            'httpCode' => Redirect::PERMANENT,
            'publish' => true,
        ]);

        $redirect = $this->repository->findRedirect('/redirect');

        static::assertNotNull($redirect);
        static::assertSame('/target', $redirect['destination']);
        static::assertSame(Redirect::PERMANENT, (int) $redirect['httpCode']);
    }
}
