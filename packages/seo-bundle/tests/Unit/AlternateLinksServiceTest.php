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
use Runroom\SeoBundle\AlternateLinks\AlternateLinksBuilderInterface;
use Runroom\SeoBundle\AlternateLinks\AlternateLinksProviderInterface;
use Runroom\SeoBundle\AlternateLinks\AlternateLinksService;
use Runroom\SeoBundle\AlternateLinks\DefaultAlternateLinksProvider;
use Runroom\SeoBundle\Tests\App\ViewModel\DummyViewModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class AlternateLinksServiceTest extends TestCase
{
    private RequestStack $requestStack;

    /**
     * @phpstan-var MockObject&AlternateLinksProviderInterface
     */
    private MockObject&AlternateLinksProviderInterface $provider;

    private DefaultAlternateLinksProvider $defaultProvider;
    private MockObject&AlternateLinksBuilderInterface $builder;
    private AlternateLinksService $service;

    /**
     * @var array<string, mixed>
     */
    private array $context;

    protected function setUp(): void
    {
        $this->requestStack = new RequestStack();
        $this->provider = $this->createMock(AlternateLinksProviderInterface::class);
        $this->defaultProvider = new DefaultAlternateLinksProvider();
        $this->builder = $this->createMock(AlternateLinksBuilderInterface::class);

        $this->service = new AlternateLinksService(
            $this->requestStack,
            $this->getProviders(),
            $this->builder
        );

        $this->context = ['model' => new DummyViewModel()];
    }

    public function testItFindsAlternateLinksForRoute(): void
    {
        $this->configureCurrentRequest();

        $this->provider->method('providesAlternateLinks')->with('route')->willReturn(true);
        $this->builder->method('build')->with($this->provider, $this->context, 'route', [])->willReturn(['es' => 'alternate_link']);

        $alternateLinks = $this->service->build($this->context);

        static::assertSame(['es' => 'alternate_link'], $alternateLinks);
    }

    public function testItFindsAlternateLinksForRouteWithTheDefaultProvider(): void
    {
        $this->configureCurrentRequest();

        $this->provider->method('providesAlternateLinks')->with('route')->willReturn(false);
        $this->builder->method('build')->with($this->defaultProvider, $this->context, 'route', [])->willReturn(['es' => 'alternate_link']);

        $alternateLinks = $this->service->build($this->context);

        static::assertSame(['es' => 'alternate_link'], $alternateLinks);
    }

    public function testItThrowsIfNoProviderIsFound(): void
    {
        $service = new AlternateLinksService($this->requestStack, [], $this->builder);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('There is no provided selected to build alternate links');

        $service->build($this->context);
    }

    private function configureCurrentRequest(): void
    {
        $request = new Request();

        $this->requestStack->push($request);

        $request->attributes->set('_route', 'route');
    }

    /**
     * @return iterable<AlternateLinksProviderInterface>
     */
    private function getProviders(): iterable
    {
        yield $this->provider;
        yield $this->defaultProvider;
    }
}
