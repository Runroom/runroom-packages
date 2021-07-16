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

namespace Runroom\CookiesBundle\Controller;

use Runroom\CookiesBundle\Service\CookiesPageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class CookiesPageController extends AbstractController
{
    private CookiesPageService $service;

    public function __construct(CookiesPageService $service)
    {
        $this->service = $service;
    }

    public function index(): Response
    {
        $model = $this->service->getCookiesPageViewModel();

        return $this->render('@RunroomCookies/show.html.twig', [
            'model' => $model,
        ]);
    }
}
