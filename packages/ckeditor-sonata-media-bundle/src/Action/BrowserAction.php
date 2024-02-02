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
use Sonata\MediaBundle\Provider\Pool;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class BrowserAction extends AbstractController
{
    public function __construct(
        private readonly Environment $twig,
        private readonly AdminFetcherInterface $adminFetcher,
        private readonly Pool $mediaPool
    ) {}

    public function __invoke(Request $request): Response
    {
        $admin = $this->adminFetcher->get($request);

        $admin->checkAccess('list');

        $datagrid = $admin->getDatagrid();
        $filters = $request->query->all('filter');

        if (!\is_array($filters) || !\array_key_exists('context', $filters)) {
            $context = $admin->getPersistentParameter('context');
        } else {
            $context = $filters['context']['value'];
        }

        $datagrid->setValue('context', null, $context ?? $this->mediaPool->getDefaultContext());
        $datagrid->setValue('providerName', null, $admin->getPersistentParameter('provider'));

        $formats = [];

        /**
         * @var MediaInterface $media
         */
        foreach ($datagrid->getResults() as $media) {
            $id = $media->getId();
            $context = $media->getContext();

            if (null === $id || null === $context || !$this->mediaPool->hasContext($context)) {
                continue;
            }

            $formats[$id] = $this->mediaPool->getFormatNamesByContext($context);
        }

        $formView = $datagrid->getForm()->createView();

        $formRenderer = $this->twig->getRuntime(FormRenderer::class);
        $formRenderer->setTheme($formView, $admin->getFilterTheme());

        return $this->render('@RunroomCkeditorSonataMedia/browser.html.twig', [
            'action' => 'browser',
            'form' => $formView,
            'datagrid' => $datagrid,
            'formats' => $formats,
            'export_formats' => [],

            // extra parameters
            'base_template' => $admin->getTemplateRegistry()->getTemplate('layout'),
            'admin' => $admin,
        ]);
    }
}
