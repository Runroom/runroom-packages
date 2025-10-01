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

namespace Runroom\BasicPageBundle\Tests\Integration;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Runroom\BasicPageBundle\Entity\BasicPage;
use Runroom\BasicPageBundle\Factory\BasicPageFactory;
use Runroom\BasicPageBundle\Repository\BasicPageRepositoryInterface;
use Runroom\BasicPageBundle\Twig\BasicPageExtension;
use Runroom\BasicPageBundle\Twig\BasicPageRuntime;
use Twig\RuntimeLoader\RuntimeLoaderInterface;
use Twig\Test\IntegrationTestCase;
use Zenstruck\Foundry\Test\Factories;

final class BasicPageExtensionTest extends IntegrationTestCase
{
    use Factories;

    public static function getFixturesDirectory(): string
    {
        return __DIR__ . '/../Fixtures/Twig/';
    }

    #[DataProvider('getTests')]
    public function testIntegration($file, $message, $condition, $templates, $exception, $outputs, $deprecation = '')
    {
        parent::testIntegration($file, $message, $condition, $templates, $exception, $outputs, $deprecation);
    }

    #[DataProvider('getLegacyTests')]
    #[Group('legacy')]
    public function testLegacyIntegration($file, $message, $condition, $templates, $exception, $outputs, $deprecation = '')
    {
        parent::testLegacyIntegration($file, $message, $condition, $templates, $exception, $outputs, $deprecation);
    }

    protected function getExtensions(): array
    {
        return [
            new BasicPageExtension(),
        ];
    }

    protected function getRuntimeLoaders(): array
    {
        $basicPages = BasicPageFactory::createMany(2, [
            'publish' => true,
            'location' => BasicPage::LOCATION_FOOTER,
        ]);

        $basicPageRepository = static::createStub(BasicPageRepositoryInterface::class);
        $basicPageRepository->method('findPublished')->willReturn($basicPages);

        $basicPageRuntime = new BasicPageRuntime($basicPageRepository);

        $runtimeLoader = $this->createMock(RuntimeLoaderInterface::class);
        $runtimeLoader->method('load')->with(BasicPageRuntime::class)->willReturn($basicPageRuntime);

        return [$runtimeLoader];
    }
}
