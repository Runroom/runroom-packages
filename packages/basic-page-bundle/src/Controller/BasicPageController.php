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

namespace Runroom\BasicPageBundle\Controller;

use Runroom\BasicPageBundle\Service\BasicPageServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class BasicPageController extends AbstractController
{
    public function __construct(private readonly BasicPageServiceInterface $service)
    {
    }

    public function show(string $slug): Response
    {
        try {
            $model = $this->service->getBasicPageViewModel($slug);
        } catch (\Exception) {
            throw $this->createNotFoundException();
        }

        return $this->render('@RunroomBasicPage/show.html.twig', [
            'model' => $model,
        ]);
    }
}
