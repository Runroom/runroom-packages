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

namespace Runroom\SeoBundle\Tests\Integration;

use Runroom\SeoBundle\AlternateLinks\AlternateLinksServiceInterface;
use Runroom\SeoBundle\MetaInformation\MetaInformationServiceInterface;
use Runroom\SeoBundle\Twig\SeoExtension;
use Runroom\SeoBundle\Twig\SeoRuntime;
use Runroom\SeoBundle\ViewModel\MetaInformationViewModel;
use Twig\RuntimeLoader\RuntimeLoaderInterface;
use Twig\Test\IntegrationTestCase;

final class SeoExtensionTest extends IntegrationTestCase
{
    public static function getFixturesDirectory(): string
    {
        return __DIR__ . '/../Fixtures/Twig/';
    }

    #[\Override]
    protected function getExtensions(): array
    {
        return [
            new SeoExtension(),
        ];
    }

    #[\Override]
    protected function getRuntimeLoaders(): array
    {
        $alternateLinksService = static::createStub(AlternateLinksServiceInterface::class);
        $metaInformationService = static::createStub(MetaInformationServiceInterface::class);

        $metaInformation = new MetaInformationViewModel();
        $metaInformation->setTitle('seo title');
        $metaInformation->setDescription('seo description');

        $alternateLinksService->method('build')->willReturn([
            'es' => 'https://www.runroom.com',
        ]);

        $metaInformationService->method('build')->willReturn($metaInformation);

        $seoRuntime = new SeoRuntime(
            $alternateLinksService,
            $metaInformationService
        );

        $runtimeLoader = $this->createMock(RuntimeLoaderInterface::class);
        $runtimeLoader->method('load')->with(SeoRuntime::class)->willReturn($seoRuntime);

        return [$runtimeLoader];
    }
}
