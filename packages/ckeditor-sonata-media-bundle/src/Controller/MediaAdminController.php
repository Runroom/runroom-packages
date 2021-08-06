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

namespace Runroom\CkeditorSonataMediaBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Model\MediaManagerInterface;
use Sonata\MediaBundle\Provider\MediaProviderInterface;
use Sonata\MediaBundle\Provider\Pool;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/** @extends CRUDController<MediaInterface> */
final class MediaAdminController extends CRUDController
{
    private MediaManagerInterface $mediaManager;
    private Pool $mediaPool;

    public function __construct(
        MediaManagerInterface $mediaManager,
        Pool $mediaPool
    ) {
        $this->mediaManager = $mediaManager;
        $this->mediaPool = $mediaPool;
    }

    /* @todo: Simplify this when dropping support for sonata-project/admin-bundle 3 */
    public function browserAction(Request $request): Response
    {
        $this->admin->checkAccess('list');

        $datagrid = $this->admin->getDatagrid();
        $filters = $request->get('filter');

        if (null === $filters || !\array_key_exists('context', $filters)) {
            $context = $this->getPersistentParameter('context');
        } else {
            $context = $filters['context']['value'];
        }

        $datagrid->setValue('context', null, $context ?? $this->mediaPool->getDefaultContext());
        $datagrid->setValue('providerName', null, $this->getPersistentParameter('provider'));

        $formats = [];

        /** @var MediaInterface $media */
        foreach ($datagrid->getResults() as $media) {
            $id = $media->getId();
            $context = $media->getContext();

            if (null === $id || null === $context || !$this->mediaPool->hasContext($context)) {
                continue;
            }

            $formats[$id] = $this->mediaPool->getFormatNamesByContext($context);
        }

        $formView = $datagrid->getForm()->createView();

        $formRenderer = $this->get('twig')->getRuntime(FormRenderer::class);
        \assert($formRenderer instanceof FormRenderer);

        $formRenderer->setTheme($formView, $this->admin->getFilterTheme());

        return $this->renderWithExtraParams('@RunroomCkeditorSonataMedia/browser.html.twig', [
            'action' => 'browser',
            'form' => $formView,
            'datagrid' => $datagrid,
            'formats' => $formats,
        ]);
    }

    public function uploadAction(Request $request): Response
    {
        $this->admin->checkAccess('create');

        $provider = $request->get('provider');
        $file = $request->files->get('upload');

        if (!$request->isMethod('POST') || null === $provider || null === $file) {
            throw new NotFoundHttpException();
        }

        $context = $request->get('context', $this->mediaPool->getDefaultContext());

        /** @var MediaInterface */
        $media = $this->mediaManager->create();
        $media->setBinaryContent($file);
        $media->setContext($context);
        $media->setProviderName($provider);

        $this->mediaManager->save($media);
        $this->admin->createObjectSecurity($media);

        return $this->renderWithExtraParams('@RunroomCkeditorSonataMedia/upload.html.twig', [
            'action' => 'list',
            'object' => $media,
            'format' => $this->mediaPool->getProvider($provider)->getFormatName(
                $media,
                $request->get('format', MediaProviderInterface::FORMAT_REFERENCE)
            ),
        ]);
    }

    /**
     * @todo: Simplify this when dropping support for sonata-project/admin-bundle 3
     *
     * @return mixed
     */
    private function getPersistentParameter(string $name)
    {
        $parameters = $this->admin->getPersistentParameters();

        return $parameters[$name] ?? null;
    }
}
