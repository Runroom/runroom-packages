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

namespace Runroom\CookiesBundle\Tests\Integration;

use Runroom\CookiesBundle\Twig\CookiesExtension;
use Runroom\CookiesBundle\Twig\CookiesRuntime;
use Twig\RuntimeLoader\RuntimeLoaderInterface;
use Twig\Test\IntegrationTestCase;

final class CookiesExtensionTest extends IntegrationTestCase
{
    public static function getFixturesDirectory(): string
    {
        return __DIR__ . '/../Fixtures/Twig/';
    }

    #[\Override]
    protected function getExtensions(): array
    {
        return [
            new CookiesExtension(),
        ];
    }

    #[\Override]
    protected function getRuntimeLoaders(): array
    {
        $cookies = [
            'performance_cookies' => [
                'category1' => [
                    'name' => 'test',
                    'cookies' => ['test1', 'test2', 'test3'],
                ],
                'category2' => [
                    'name' => 'test2',
                    'cookies' => ['test4', 'test5', 'test6'],
                ],
            ],
            'targeting_cookies' => [
                'category1' => [
                    'name' => 'test',
                    'cookies' => ['test1', 'test2', 'test3'],
                ],
                'category2' => [
                    'name' => 'test2',
                    'cookies' => ['test4', 'test5', 'test6'],
                ],
            ],
        ];

        $cookiesRuntime = new CookiesRuntime($cookies);

        $runtimeLoader = $this->createMock(RuntimeLoaderInterface::class);
        $runtimeLoader->method('load')->with(CookiesRuntime::class)->willReturn($cookiesRuntime);

        return [$runtimeLoader];
    }
}
