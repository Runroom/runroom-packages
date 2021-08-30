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

class SeoExtensionTest extends IntegrationTestCase
{
    public function getFixturesDir()
    {
        return __DIR__ . '/../Fixtures/Twig/';
    }

    protected function getExtensions()
    {
        return [
            new SeoExtension(),
        ];
    }

    protected function getRuntimeLoaders()
    {
        $alternateLinksService = $this->createStub(AlternateLinksServiceInterface::class);
        $metaInformationService = $this->createStub(MetaInformationServiceInterface::class);

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
