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
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Runroom\SeoBundle\AlternateLinks\AbstractAlternateLinksProvider;
use Runroom\SeoBundle\AlternateLinks\AlternateLinksBuilder;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AlternateLinksBuilderTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<UrlGeneratorInterface> */
    private $urlGenerator;

    /** @var string[] */
    private $locales;

    /** @var DummyAlternateLinksProvider */
    private $provider;

    /** @var AlternateLinksBuilder */
    private $builder;

    protected function setUp(): void
    {
        $this->urlGenerator = $this->prophesize(UrlGeneratorInterface::class);
        $this->locales = ['es', 'en'];

        $this->provider = new DummyAlternateLinksProvider();
        $this->builder = new AlternateLinksBuilder(
            $this->urlGenerator->reveal(),
            $this->locales
        );
    }

    /** @test */
    public function itDoesNotProvideAnyAlternateLinks(): void
    {
        self::assertFalse($this->provider->providesAlternateLinks('default'));
    }

    /** @test */
    public function itFindsAlternateLinksForRoute(): void
    {
        $route = 'dummy_route';

        foreach ($this->locales as $locale) {
            $this->urlGenerator->generate($route . '.' . $locale, [
                'dummy_param' => 'dummy_value',
                'dummy_query' => 'dummy_value',
            ], UrlGeneratorInterface::ABSOLUTE_URL)->willReturn($locale);
        }

        $alternateLinks = $this->builder->build($this->provider, 'model', $route);

        foreach ($this->locales as $locale) {
            self::assertContains($locale, $alternateLinks);
        }
    }

    /** @test */
    public function itReturnsEmptyAlternateLinksIfRouteDoesNotExist(): void
    {
        $this->urlGenerator->generate(Argument::cetera())->willThrow(RouteNotFoundException::class);

        self::assertEmpty($this->builder->build($this->provider, 'model', 'missing_route'));
    }
}

class DummyAlternateLinksProvider extends AbstractAlternateLinksProvider
{
    /** @return array<string, string>|null */
    public function getParameters($model, string $locale): ?array
    {
        return [
            'dummy_param' => 'dummy_value',
            'dummy_query' => 'dummy_value',
        ];
    }

    /** @return string[] */
    protected function getRoutes(): array
    {
        return ['dummy_route'];
    }
}
