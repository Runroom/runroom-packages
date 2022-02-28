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

namespace Runroom\TranslationBundle\Tests\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Runroom\TranslationBundle\Factory\TranslationFactory;
use Runroom\TranslationBundle\Repository\TranslationRepository;
use Runroom\TranslationBundle\Service\TranslationService;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zenstruck\Foundry\Test\Factories;

class TranslationServiceTest extends TestCase
{
    use Factories;

    /**
     * @var MockObject&TranslationRepository
     */
    private $repository;

    /**
     * @var MockObject&TranslatorInterface
     */
    private $translator;

    private TranslationService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(TranslationRepository::class);
        $this->translator = $this->createMock(TranslatorInterface::class);

        $this->service = new TranslationService(
            $this->repository,
            $this->translator
        );
    }

    /**
     * @test
     */
    public function itReturnsAStringTranslatedByTheRepository(): void
    {
        $translation = TranslationFactory::new(['key' => 'key'])->withTranslations(['en'])->create();

        $this->repository->method('findOneBy')->with(['key' => 'key'])->willReturn($translation);
        $this->translator->expects(static::never())->method('trans')->with('key', [], null, 'en');

        $result = $this->service->translate('key', [], 'en');

        static::assertSame($translation->getValue(), $result);
    }

    /**
     * @test
     */
    public function itReturnsAStringTranslatedByTheTranslatorComponent(): void
    {
        $this->repository->method('findOneBy')->with(['key' => 'key'])->willReturn(null);
        $this->translator->method('trans')->with('key', [], null, 'en')
            ->willReturn('another_translation');

        $result = $this->service->translate('key', [], 'en');

        static::assertSame('another_translation', $result);
    }
}
