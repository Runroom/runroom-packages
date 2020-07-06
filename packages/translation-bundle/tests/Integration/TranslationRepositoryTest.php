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

namespace Runroom\TranslationBundle\Tests\Integration;

use Runroom\Testing\TestCase\DoctrineTestCase;
use Runroom\TranslationBundle\Repository\TranslationRepository;

class TranslationRepositoryTest extends DoctrineTestCase
{
    /** @var TranslationRepository */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = static::$container->get(TranslationRepository::class);
    }

    /** @test */
    public function itFindsTranslationsByKey(): void
    {
        $translation = $this->repository->findOneBy(['key' => 'test']);

        if (null !== $translation) {
            self::assertSame(1, $translation->getId());
            self::assertSame('test', $translation->__toString());
            self::assertNotNull($translation->getValue());
        } else {
            self::fail('not found translation');
        }
    }

    protected function getDataFixtures(): array
    {
        return ['translations.yaml'];
    }
}
