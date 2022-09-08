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

use Sonata\AdminBundle\Admin\AdminInterface;
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
    private Environment $twig;
    private AdminFetcherInterface $adminFetcher;
    private Pool $mediaPool;

    public function __construct(
        Environment $twig,
        AdminFetcherInterface $adminFetcher,
        Pool $mediaPool
    ) {
        $this->twig = $twig;
        $this->adminFetcher = $adminFetcher;
        $this->mediaPool = $mediaPool;
    }

    /**
     * @todo: Simplify this when dropping support for sonata-project/admin-bundle 3
     */
    public function __invoke(Request $request): Response
    {
        $admin = $this->adminFetcher->get($request);

        $admin->checkAccess('list');

        $datagrid = $admin->getDatagrid();
        // @todo: Change to $request->query->all('filter') when support for Symfony < 5.1 is dropped.
        $filters = $request->query->all()['filter'] ?? [];

        if (!\is_array($filters) || !\array_key_exists('context', $filters)) {
            $context = $this->getPersistentParameter($admin, 'context');
        } else {
            $context = $filters['context']['value'];
        }

        $datagrid->setValue('context', null, $context ?? $this->mediaPool->getDefaultContext());
        $datagrid->setValue('providerName', null, $this->getPersistentParameter($admin, 'provider'));

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
            'base_template' => $this->getBaseTemplate($request, $admin),
            'admin' => $admin,
        ]);
    }

    /**
     * @todo: Simplify this when dropping support for sonata-project/admin-bundle 3
     *
     * @param AdminInterface<object> $admin
     *
     * @psalm-suppress UndefinedMethod
     */
    private function getBaseTemplate(Request $request, AdminInterface $admin): string
    {
        if ($request->isXmlHttpRequest()) {
            // @phpstan-ignore-next-line
            return method_exists($admin, 'getTemplateRegistry') ? $admin->getTemplateRegistry()->getTemplate('ajax') : $admin->getTemplate('ajax');
        }

        // @phpstan-ignore-next-line
        return method_exists($admin, 'getTemplateRegistry') ? $admin->getTemplateRegistry()->getTemplate('layout') : $admin->getTemplate('layout');
    }

    /**
     * @todo: Simplify this when dropping support for sonata-project/admin-bundle 3
     *
     * @param AdminInterface<object> $admin
     *
     * @return mixed
     */
    private function getPersistentParameter(AdminInterface $admin, string $name)
    {
        $parameters = $admin->getPersistentParameters();

        return $parameters[$name] ?? null;
    }
}
