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

use Runroom\CookiesBundle\Factory\CookiesPageFactory;
use Runroom\CookiesBundle\Repository\CookiesPageRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class CookiesPageRepositoryTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    private CookiesPageRepository $repository;

    protected function setUp(): void
    {
        $this->repository = static::getContainer()->get(CookiesPageRepository::class);
    }

    public function testIfFindsCookiesPageById(): void
    {
        CookiesPageFactory::new()->withTranslations(['en'])->create();

        $cookiesPage = $this->repository->find(1);

        static::assertNotNull($cookiesPage);
        static::assertSame(1, $cookiesPage->getId());
        static::assertNotEmpty((string) $cookiesPage);
        static::assertNotNull($cookiesPage->getContent());
    }
}
