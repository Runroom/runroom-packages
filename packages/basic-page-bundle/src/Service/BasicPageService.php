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

namespace Runroom\BasicPageBundle\Service;

use Runroom\BasicPageBundle\Repository\BasicPageRepository;
use Runroom\BasicPageBundle\ViewModel\BasicPageViewModel;
use Runroom\RenderEventBundle\Event\PageRenderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/** @final */
class BasicPageService implements EventSubscriberInterface
{
    /** @var BasicPageRepository */
    private $repository;

    public function __construct(BasicPageRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getBasicPageViewModel(string $slug): BasicPageViewModel
    {
        $basicPage = $this->repository->findBySlug($slug);

        $model = new BasicPageViewModel();
        $model->setBasicPage($basicPage);

        return $model;
    }

    public function onPageRender(PageRenderEvent $event): void
    {
        $page = $event->getPageViewModel();

        $page->addContext('basic_pages', $this->repository->findBy(['publish' => true]));

        $event->setPageViewModel($page);
    }

    public static function getSubscribedEvents()
    {
        return [
            PageRenderEvent::EVENT_NAME => 'onPageRender',
        ];
    }
}
