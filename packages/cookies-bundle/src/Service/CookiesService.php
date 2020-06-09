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

namespace Runroom\CookiesBundle\Service;

use Runroom\RenderEventBundle\Event\PageRenderEvent;
use Runroom\CookiesBundle\ViewModel\CookiesViewModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CookiesService implements EventSubscriberInterface
{
    protected const TYPE_PERFORMANCE = 'performance_cookies';
    protected const TYPE_TARGETING = 'targeting_cookies';

    protected $cookies;

    public function __construct(array $cookies)
    {
        $this->cookies = $cookies;
    }

    public function onPageRender(PageRenderEvent $event): void
    {
        $page = $event->getPageViewModel();
        $page->setCookies($this->buildCookiesViewModel());
        $event->setPageViewModel($page);
    }

    public static function getSubscribedEvents()
    {
        return [
            PageRenderEvent::EVENT_NAME => 'onPageRender',
        ];
    }

    protected function buildCookiesViewModel(): CookiesViewModel
    {
        $model = new CookiesViewModel();
        $model->setPerformanceCookies($this->getCookies(self::TYPE_PERFORMANCE));
        $model->setTargetingCookies($this->getCookies(self::TYPE_TARGETING));

        return $model;
    }

    protected function getCookies(string $type): array
    {
        $cookies = [];
        foreach ($this->cookies[$type] as $category) {
            $cookies = array_merge($cookies, $category['cookies'] ?? []);
        }

        return $cookies;
    }
}
