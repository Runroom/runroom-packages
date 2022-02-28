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

namespace Runroom\RenderEventBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Runroom\RenderEventBundle\ViewModel\PageViewModel;

class PageViewModelTest extends TestCase
{
    private PageViewModel $viewModel;

    protected function setUp(): void
    {
        $this->viewModel = new PageViewModel();
    }

    /**
     * @test
     */
    public function itSetContent(): void
    {
        $this->viewModel->setContent('content');

        static::assertSame('content', $this->viewModel->getContent());
    }

    /**
     * @test
     */
    public function itAddsContext(): void
    {
        $this->viewModel->addContext('test', 'content');

        static::assertSame('content', $this->viewModel->getContext('test'));
        static::assertNull($this->viewModel->getContext('no_context'));
    }
}
