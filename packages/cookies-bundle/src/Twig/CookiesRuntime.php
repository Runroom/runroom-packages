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

namespace Runroom\CookiesBundle\Twig;

use Runroom\CookiesBundle\DependencyInjection\Configuration;
use Runroom\CookiesBundle\ViewModel\CookiesViewModel;
use Twig\Extension\RuntimeExtensionInterface;

/**
 * @phpstan-import-type CookiesData from Configuration
 */
final readonly class CookiesRuntime implements RuntimeExtensionInterface
{
    private const string TYPE_PERFORMANCE = 'performance_cookies';
    private const string TYPE_TARGETING = 'targeting_cookies';

    /**
     * @phpstan-param CookiesData $cookies
     */
    public function __construct(private array $cookies) {}

    public function buildCookies(): CookiesViewModel
    {
        $model = new CookiesViewModel();
        $model->setPerformanceCookies($this->getCookies(self::TYPE_PERFORMANCE));
        $model->setTargetingCookies($this->getCookies(self::TYPE_TARGETING));

        return $model;
    }

    /**
     * @return string[]
     */
    private function getCookies(string $type): array
    {
        $cookies = [];
        foreach ($this->cookies[$type] as $category) {
            $cookies = array_merge($cookies, $category['cookies']);
        }

        return $cookies;
    }
}
