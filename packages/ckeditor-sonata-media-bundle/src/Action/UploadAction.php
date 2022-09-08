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

namespace Runroom\CkeditorSonataMediaBundle\Action;

use Sonata\AdminBundle\Request\AdminFetcherInterface;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Model\MediaManagerInterface;
use Sonata\MediaBundle\Provider\Pool;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class UploadAction extends AbstractController
{
    private AdminFetcherInterface $adminFetcher;
    private MediaManagerInterface $mediaManager;
    private Pool $mediaPool;

    public function __construct(
        AdminFetcherInterface $adminFetcher,
        MediaManagerInterface $mediaManager,
        Pool $mediaPool
    ) {
        $this->adminFetcher = $adminFetcher;
        $this->mediaManager = $mediaManager;
        $this->mediaPool = $mediaPool;
    }

    public function __invoke(Request $request): Response
    {
        $admin = $this->adminFetcher->get($request);

        $admin->checkAccess('create');

        $provider = $request->query->get('provider');
        $file = $request->files->get('upload');

        if (!$request->isMethod('POST') || null === $provider || null === $file) {
            throw new NotFoundHttpException();
        }

        $context = $request->query->get('context', $this->mediaPool->getDefaultContext());

        /**
         * @var MediaInterface
         */
        $media = $this->mediaManager->create();
        $media->setBinaryContent($file);
        $media->setContext($context);
        $media->setProviderName($provider);

        $this->mediaManager->save($media);
        $admin->createObjectSecurity($media);

        return $this->render('@RunroomCkeditorSonataMedia/upload.html.twig', [
            'object' => $media,
        ]);
    }
}
