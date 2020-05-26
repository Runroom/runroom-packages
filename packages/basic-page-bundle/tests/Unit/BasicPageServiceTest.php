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

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Runroom\BasicPageBundle\Entity\BasicPage;
use Runroom\BasicPageBundle\Repository\BasicPageRepository;
use Runroom\BasicPageBundle\Service\BasicPageService;
use Runroom\BasicPageBundle\ViewModel\BasicPageViewModel;

class BasicPageServiceTest extends TestCase
{
    use ProphecyTrait;

    protected const STATIC_SLUG = 'slug';

    protected $repository;
    protected $service;

    protected function setUp(): void
    {
        $this->repository = $this->prophesize(BasicPageRepository::class);

        $this->service = new BasicPageService($this->repository->reveal());
    }

    /**
     * @test
     */
    public function itGetsStaticViewModel()
    {
        $BasicPage = new BasicPage();

        $this->repository->findBySlug(self::STATIC_SLUG)->willReturn($BasicPage);

        $model = $this->service->getBasicPageViewModel(self::STATIC_SLUG);

        $this->assertInstanceOf(BasicPageViewModel::class, $model);
        $this->assertSame($BasicPage, $model->getBasicPage());
    }
}
