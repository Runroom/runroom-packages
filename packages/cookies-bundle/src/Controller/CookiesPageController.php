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

use Runroom\CookiesBundle\Service\CookiesPageServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class CookiesPageController extends AbstractController
{
    public function __construct(private readonly CookiesPageServiceInterface $service)
    {
    }

    public function index(): Response
    {
        $model = $this->service->getCookiesPageViewModel();

        return $this->render('@RunroomCookies/show.html.twig', [
            'model' => $model,
        ]);
    }
}
