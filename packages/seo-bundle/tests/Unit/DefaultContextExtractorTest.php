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

namespace Runroom\SeoBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Runroom\SeoBundle\Context\DefaultContextExtractor;
use Runroom\SeoBundle\Model\SeoModelInterface;
use Runroom\SeoBundle\Tests\App\ViewModel\DummyViewModel;

class DefaultContextExtractorTest extends TestCase
{
    private DefaultContextExtractor $extractor;

    protected function setUp(): void
    {
        $this->extractor = new DefaultContextExtractor('model');
    }

    /** @test */
    public function itReturnsNullifNoModelKeyIsFound(): void
    {
        self::assertNull($this->extractor->extract([]));
    }

    /** @test */
    public function itThrowsifModelKeyIsNotSeoModelInterface(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Model is not an instance of: ' . SeoModelInterface::class);

        $this->extractor->extract(['model' => 'not_seo_model']);
    }

    /** @test */
    public function itExtractsModelFromContext(): void
    {
        $viewModel = new DummyViewModel();

        self::assertSame($viewModel, $this->extractor->extract(['model' => $viewModel]));
    }
}
