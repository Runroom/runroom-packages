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
use Runroom\TranslationBundle\Service\TranslationService;
use Runroom\TranslationBundle\Twig\TranslationExtension;

class TranslationExtensionTest extends TestCase
{
    /** @var MockObject&TranslationService */
    private $service;

    private TranslationExtension $extension;

    protected function setUp(): void
    {
        $this->service = $this->createMock(TranslationService::class);

        $this->extension = new TranslationExtension($this->service);
    }

    /** @test */
    public function itTranslates(): void
    {
        $this->service->method('translate')->with('key', [], null)->willReturn('value');

        $result = $this->extension->translate('key');

        static::assertSame('value', $result);
    }

    /** @test */
    public function itDefinesAFilter(): void
    {
        $filters = $this->extension->getFilters();

        static::assertCount(1, $filters);
    }
}
