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
use Runroom\BasicPageBundle\Factory\BasicPageFactory;
use Runroom\BasicPageBundle\Repository\BasicPageRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class BasicPageRepositoryTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    private BasicPageRepository $repository;

    protected function setUp(): void
    {
        parent::bootKernel();

        $this->repository = self::$container->get(BasicPageRepository::class);
    }

    /** @test */
    public function itFindsBasicPageGivenItsSlug(): void
    {
        BasicPageFactory::new(['publish' => true])->withTranslations(['en'], [
            'slug' => 'slug',
        ])->create();

        $basicPage = $this->repository->findBySlug('slug');

        self::assertSame(1, $basicPage->getId());
        self::assertNotEmpty((string) $basicPage);
        self::assertNotNull($basicPage->getLocation());
        self::assertNotNull($basicPage->getContent());
        self::assertNotNull($basicPage->getSlug());
        self::assertIsBool($basicPage->getPublish());
    }

    /** @test */
    public function itDoesNotFindUnPublishedStatigPage(): void
    {
        $this->expectException(NoResultException::class);

        $this->repository->findBySlug('unpublished');
    }
}
