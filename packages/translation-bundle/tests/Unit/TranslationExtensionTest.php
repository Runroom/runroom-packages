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
use Runroom\TranslationBundle\Service\TranslationServiceInterface;
use Runroom\TranslationBundle\Twig\TranslationExtension;

final class TranslationExtensionTest extends TestCase
{
    private MockObject&TranslationServiceInterface $service;
    private TranslationExtension $extension;

    protected function setUp(): void
    {
        $this->service = $this->createMock(TranslationServiceInterface::class);

        $this->extension = new TranslationExtension($this->service);
    }

    public function testItTranslates(): void
    {
        $this->service->method('translate')->with('key', [], null)->willReturn('value');

        $result = $this->extension->translate('key');

        static::assertSame('value', $result);
    }

    public function testItDefinesAFilter(): void
    {
        $filters = $this->extension->getFilters();

        static::assertCount(1, $filters);
    }
}
