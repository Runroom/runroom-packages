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
use Runroom\TranslationBundle\Repository\TranslationRepository;
use Runroom\TranslationBundle\Service\TranslationService;
use Runroom\TranslationBundle\Tests\Fixtures\TranslationFixtures;
use Symfony\Contracts\Translation\TranslatorInterface;

class TranslationServiceTest extends TestCase
{
    /** @var MockObject&TranslationRepository */
    private $repository;

    /** @var MockObject&TranslatorInterface */
    private $translator;

    /** @var TranslationService */
    private $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(TranslationRepository::class);
        $this->translator = $this->createMock(TranslatorInterface::class);

        $this->service = new TranslationService(
            $this->repository,
            $this->translator
        );
    }

    /** @test */
    public function itReturnsAStringTranslatedByTheRepository(): void
    {
        $translation = TranslationFixtures::create();

        $this->repository->method('findOneBy')->with(['key' => TranslationFixtures::KEY])->willReturn($translation);
        $this->translator->expects(self::never())->method('trans')->with(TranslationFixtures::KEY, [], null, 'en');

        $result = $this->service->translate(TranslationFixtures::KEY, [], 'en');

        self::assertSame(TranslationFixtures::VALUE, $result);
    }

    /** @test */
    public function itReturnsAStringTranslatedByTheTranslatorComponent(): void
    {
        $this->repository->method('findOneBy')->with(['key' => TranslationFixtures::KEY])->willReturn(null);
        $this->translator->method('trans')->with(TranslationFixtures::KEY, [], null, 'en')
            ->willReturn('another_translation');

        $result = $this->service->translate(TranslationFixtures::KEY, [], 'en');

        self::assertSame('another_translation', $result);
    }
}
