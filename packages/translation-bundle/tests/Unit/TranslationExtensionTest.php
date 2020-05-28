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

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Runroom\TranslationBundle\Service\TranslationService;
use Runroom\TranslationBundle\Tests\Fixtures\TranslationFixtures;
use Runroom\TranslationBundle\Twig\TranslationExtension;

class TranslationExtensionTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<TranslationService> */
    private $service;

    /** @var TranslationExtension */
    private $extension;

    protected function setUp(): void
    {
        $this->service = $this->prophesize(TranslationService::class);

        $this->extension = new TranslationExtension($this->service->reveal());
    }

    /**
     * @test
     */
    public function itTranslates(): void
    {
        $this->service->translate(TranslationFixtures::KEY, [], null)->willReturn(TranslationFixtures::VALUE);

        $result = $this->extension->translate(TranslationFixtures::KEY);

        $this->assertSame(TranslationFixtures::VALUE, $result);
    }

    /**
     * @test
     */
    public function itDefinesAFilter(): void
    {
        $filters = $this->extension->getFilters();

        $this->assertCount(1, $filters);
    }
}
