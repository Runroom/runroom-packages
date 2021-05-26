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

use Runroom\TranslationBundle\Factory\TranslationFactory;
use Runroom\TranslationBundle\Repository\TranslationRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class TranslationRepositoryTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    /** @var TranslationRepository */
    private $repository;

    protected function setUp(): void
    {
        parent::bootKernel();

        $this->repository = static::$container->get(TranslationRepository::class);
    }

    /** @test */
    public function itFindsTranslationsByKey(): void
    {
        TranslationFactory::new(['key' => 'test'])->withTranslations(['en'])->create();

        $translation = $this->repository->findOneBy(['key' => 'test']);

        if (null !== $translation) {
            self::assertSame(1, $translation->getId());
            self::assertSame('test', (string) $translation);
            self::assertNotNull($translation->getValue());
        } else {
            self::fail('not found translation');
        }
    }
}
