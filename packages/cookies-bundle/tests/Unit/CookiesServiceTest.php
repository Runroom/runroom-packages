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

namespace Tests\Runroom\CookiesBundle\Unit;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Runroom\CookiesBundle\Service\CookiesService;
use Runroom\CookiesBundle\ViewModel\CookiesViewModel;
use Runroom\RenderEventBundle\Event\PageRenderEvent;
use Runroom\RenderEventBundle\ViewModel\PageViewModel;
use Symfony\Component\HttpFoundation\Response;

class CookiesServiceTest extends TestCase
{
    use ProphecyTrait;

    /** @var string[] */
    private const PERFORMANCE_COOKIES = ['cookie 1', 'cookie 2', 'cookie 3', 'cookie 4'];

    /** @var string[] */
    private const TARGETING_COOKIES = ['cookie 5', 'cookie 6', 'cookie 7', 'cookie 8'];

    /** @var CookiesService */
    private $service;

    protected function setUp(): void
    {
        $this->service = new CookiesService($this->buildCookiesArray());
    }

    /** @test */
    public function itSetsCookies(): void
    {
        $event = $this->configurePageRenderEvent();

        $this->service->onPageRender($event);

        $cookies = $event->getPageViewModel()->getContext('cookies');

        self::assertInstanceOf(CookiesViewModel::class, $cookies);
        self::assertSame(self::PERFORMANCE_COOKIES, $cookies->getPerformanceCookies());
        self::assertSame(self::TARGETING_COOKIES, $cookies->getTargetingCookies());
    }

    private function configurePageRenderEvent(): PageRenderEvent
    {
        $response = $this->prophesize(Response::class);
        $page = new PageViewModel();

        return new PageRenderEvent('view', $page, $response->reveal());
    }

    /** @return array<string, array{ name: string, cookies: string[]}[]> */
    private function buildCookiesArray(): array
    {
        return [
            'performance_cookies' => [
                ['name' => 'category 1', 'cookies' => ['cookie 1', 'cookie 2']],
                ['name' => 'category 2', 'cookies' => ['cookie 3', 'cookie 4']],
            ],
            'targeting_cookies' => [
                ['name' => 'category 3', 'cookies' => ['cookie 5', 'cookie 6']],
                ['name' => 'category 4', 'cookies' => ['cookie 7', 'cookie 8']],
            ],
        ];
    }
}
