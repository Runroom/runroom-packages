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

    private TranslationRepository $repository;

    protected function setUp(): void
    {
        parent::bootKernel();

        /**
         * @todo: Simplify this when dropping support for Symfony 4
         *
         * @phpstan-ignore-next-line
         */
        $container = method_exists(static::class, 'getContainer') ? static::getContainer() : static::$container;

        $this->repository = $container->get(TranslationRepository::class);
    }

    public function testItFindsTranslationsByKey(): void
    {
        TranslationFactory::new(['key' => 'test'])->withTranslations(['en'])->create();

        $translation = $this->repository->findOneBy(['key' => 'test']);

        if (null !== $translation) {
            static::assertSame(1, $translation->getId());
            static::assertSame('test', (string) $translation);
            static::assertNotNull($translation->getValue());
        } else {
            static::fail('not found translation');
        }
    }
}
