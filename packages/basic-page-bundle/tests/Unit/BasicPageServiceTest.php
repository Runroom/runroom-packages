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

namespace Runroom\BasicPageBundle\Tests\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Runroom\BasicPageBundle\Entity\BasicPage;
use Runroom\BasicPageBundle\Repository\BasicPageRepositoryInterface;
use Runroom\BasicPageBundle\Service\BasicPageService;

final class BasicPageServiceTest extends TestCase
{
    private MockObject&BasicPageRepositoryInterface $repository;
    private BasicPageService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(BasicPageRepositoryInterface::class);

        $this->service = new BasicPageService($this->repository);
    }

    public function testItGetsBasicPage(): void
    {
        $basicPage = new BasicPage();

        $this->repository->method('findBySlug')->with('slug')->willReturn($basicPage);

        $model = $this->service->getBasicPageViewModel('slug');

        static::assertSame($basicPage, $model->getBasicPage());
    }
}
