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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Runroom\SeoBundle\AlternateLinks\AlternateLinksBuilder;
use Runroom\SeoBundle\Tests\App\AlternateLinks\DummyAlternateLinksProvider;
use Runroom\SeoBundle\Tests\App\ViewModel\DummyViewModel;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class AlternateLinksBuilderTest extends TestCase
{
    private MockObject&UrlGeneratorInterface $urlGenerator;

    /**
     * @var string[]
     */
    private array $locales;

    private DummyAlternateLinksProvider $provider;
    private AlternateLinksBuilder $builder;

    protected function setUp(): void
    {
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->locales = ['es', 'en'];

        $this->provider = new DummyAlternateLinksProvider();
        $this->builder = new AlternateLinksBuilder(
            $this->urlGenerator,
            $this->locales
        );
    }

    public function testItDoesNotProvideAnyAlternateLinks(): void
    {
        static::assertFalse($this->provider->providesAlternateLinks('default'));
    }

    public function testItFindsAlternateLinksForRoute(): void
    {
        $route = 'dummy_route';

        $this->urlGenerator->expects(static::exactly(2))->method('generate')->willReturnMap(
            array_map(
                static fn (string $locale): array => [
                    $route . '.' . $locale,
                    [
                        'dummy_param' => 'dummy_value',
                        'dummy_query' => 'dummy_value',
                    ],
                    UrlGeneratorInterface::ABSOLUTE_URL,
                    $locale,
                ],
                $this->locales
            )
        );

        $alternateLinks = $this->builder->build($this->provider, ['model' => new DummyViewModel()], $route);

        foreach ($this->locales as $locale) {
            static::assertContains($locale, $alternateLinks);
        }
    }

    public function testItReturnsEmptyAlternateLinksIfRouteDoesNotExist(): void
    {
        $this->urlGenerator->method('generate')->willThrowException(new RouteNotFoundException());

        static::assertEmpty($this->builder->build($this->provider, ['model' => new DummyViewModel()], 'missing_route'));
    }
}
