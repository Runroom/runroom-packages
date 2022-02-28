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

namespace Runroom\CkeditorSonataMediaBundle\Tests\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Runroom\CkeditorSonataMediaBundle\Controller\MediaAdminController;
use Runroom\CkeditorSonataMediaBundle\Tests\App\Entity\Media;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\BreadcrumbsBuilderInterface;
use Sonata\AdminBundle\Admin\Pool as AdminPool;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Request\AdminFetcher;
use Sonata\AdminBundle\Templating\TemplateRegistry;
use Sonata\AdminBundle\Templating\TemplateRegistryAwareInterface;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Model\MediaManagerInterface;
use Sonata\MediaBundle\Provider\MediaProviderInterface;
use Sonata\MediaBundle\Provider\Pool as MediaPool;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

class MediaAdminControllerTest extends TestCase
{
    private Container $container;

    /**
     * @var MockObject&AdminInterface<object>
     */
    private $admin;

    private Request $request;

    /**
     * @var MockObject&MediaManagerInterface
     */
    private $mediaManager;

    /**
     * @var MediaPool
     */
    private $mediaPool;

    /**
     * @var MockObject&Environment
     */
    private $twig;

    private MediaAdminController $controller;

    protected function setUp(): void
    {
        $this->container = new Container();
        $this->admin = $this->createMock(AdminInterface::class);
        $this->request = new Request();
        $this->mediaManager = $this->createMock(MediaManagerInterface::class);
        $this->mediaPool = new MediaPool('context');
        $this->twig = $this->createMock(Environment::class);

        $this->configureAdmin();
        $this->configureRequest();
        $this->configureContainer();

        $this->controller = new MediaAdminController(
            $this->mediaManager,
            $this->mediaPool
        );
        $this->controller->setContainer($this->container);
        $this->controller->configureAdmin($this->request);
    }

    /**
     * @test
     */
    public function browserAction(): void
    {
        $datagrid = $this->createMock(DatagridInterface::class);
        $form = $this->createStub(Form::class);
        $formView = new FormView();

        $media = new Media();
        $media->setId(1);
        $media->setContext('context');

        $media2 = new Media();
        $media2->setId(2);
        $media2->setContext('context2');

        $this->mediaPool->addContext('context', [], ['format1' => [
            'width' => null,
            'height' => null,
            'quality' => 80,
            'format' => 'jpg',
            'constraint' => true,
            'resizer' => null,
            'resizer_options' => [],
        ]]);

        $this->configureSetFormTheme($formView, ['filterTheme']);
        $this->configureRender('@RunroomCkeditorSonataMedia/browser.html.twig', 'renderResponse');
        $datagrid->expects(static::exactly(2))->method('setValue')->withConsecutive(
            ['context', null, 'another_context'],
            ['providerName', null, null]
        );
        $datagrid->method('getResults')->willReturn([new Media(), $media, $media2]);
        $datagrid->method('getForm')->willReturn($form);
        $form->method('createView')->willReturn($formView);
        $this->admin->expects(static::once())->method('checkAccess')->with('list');
        $this->admin->method('getDatagrid')->willReturn($datagrid);
        $this->admin->method('getFilterTheme')->willReturn(['filterTheme']);

        $response = $this->controller->browserAction($this->request);

        static::assertSame('renderResponse', $response->getContent());
    }

    /**
     * @test
     */
    public function browserActionWithFilters(): void
    {
        $datagrid = $this->createMock(DatagridInterface::class);
        $form = $this->createStub(Form::class);
        $formView = new FormView();

        $this->request->query->set('filter', ['context' => [
            'value' => 'context',
        ]]);

        $this->configureSetFormTheme($formView, ['filterTheme']);
        $this->configureRender('@RunroomCkeditorSonataMedia/browser.html.twig', 'renderResponse');
        $datagrid->expects(static::exactly(2))->method('setValue')->withConsecutive(
            ['context', null, 'context'],
            ['providerName', null, null]
        );
        $datagrid->method('getResults')->willReturn([]);
        $datagrid->method('getForm')->willReturn($form);
        $form->method('createView')->willReturn($formView);
        $this->admin->expects(static::once())->method('checkAccess')->with('list');
        $this->admin->method('getDatagrid')->willReturn($datagrid);
        $this->admin->method('getFilterTheme')->willReturn(['filterTheme']);

        $response = $this->controller->browserAction($this->request);

        static::assertSame('renderResponse', $response->getContent());
    }

    /**
     * @test
     */
    public function uploadAction(): void
    {
        $media = $this->createStub(MediaInterface::class);
        $provider = $this->createStub(MediaProviderInterface::class);

        $this->configureRender('@RunroomCkeditorSonataMedia/upload.html.twig', 'renderResponse');

        $this->mediaPool->addProvider('provider', $provider);

        $this->mediaManager->method('create')->willReturn($media);
        $this->mediaManager->expects(static::once())->method('save')->with($media);
        $this->admin->expects(static::once())->method('checkAccess')->with('create');
        $this->admin->expects(static::once())->method('createObjectSecurity')->with($media);

        $response = $this->controller->uploadAction($this->request);

        static::assertSame('renderResponse', $response->getContent());
    }

    /**
     * @test
     */
    public function uploadActionThrowsWhenNoPostMethod(): void
    {
        $this->request->setMethod('GET');

        $this->expectException(NotFoundHttpException::class);

        $this->controller->uploadAction($this->request);
    }

    /* @todo: Simplify when dropping support for sonata-project/admin-bundle 3 */
    private function configureAdmin(): void
    {
        if (method_exists(AdminInterface::class, 'getTemplate')) {
            $this->admin->method('getTemplate')->with('layout')->willReturn('layout.html.twig');
        }

        /* @phpstan-ignore-next-line */
        if (method_exists(TemplateRegistryAwareInterface::class, 'hasTemplateRegistry')) {
            $this->admin->method('hasTemplateRegistry')->willReturn(true);
        }

        $this->admin->method('isChild')->willReturn(false);
        $this->admin->method('setRequest')->with($this->request);
        $this->admin->method('getCode')->willReturn('admin_code');
    }

    private function configureRequest(): void
    {
        $upload = $this->createStub(UploadedFile::class);

        $this->request->query->set('_sonata_admin', 'admin_code');
        $this->request->query->set('provider', 'provider');
        $this->request->files->set('upload', $upload);
        $this->request->setMethod('POST');
    }

    /**
     * @param string[] $formTheme
     */
    private function configureSetFormTheme(FormView $formView, array $formTheme): void
    {
        $twigRenderer = $this->createMock(FormRenderer::class);

        $this->twig->method('getRuntime')->with(FormRenderer::class)->willReturn($twigRenderer);

        $twigRenderer->expects(static::once())->method('setTheme')->with($formView, $formTheme);
    }

    /* @todo: Simplify when dropping support for sonata-project/admin-bundle 3 */
    private function configureRender(string $template, string $rendered): void
    {
        $this->admin->method('getPersistentParameters')->willReturn([
            'param' => 'param',
            'context' => 'another_context',
        ]);
        $this->twig->method('render')->with($template, static::isType('array'))->willReturn($rendered);
    }

    /* @todo: Simplify when dropping support for sonata-project/admin-bundle 3 */
    private function configureContainer(): void
    {
        $pool = new AdminPool($this->container, [
            'admin_code' => 'admin_code',
        ]);
        $breadcrumbsBuilder = $this->createStub(BreadcrumbsBuilderInterface::class);

        $requestStack = new RequestStack();
        $requestStack->push($this->request);

        $this->container->setParameter('kernel.bundles', ['SonataMediaBundle' => true]);
        $this->container->set('admin_code', $this->admin);
        $this->container->set('request_stack', $requestStack);
        $this->container->set('sonata.media.pool', $this->mediaPool);
        $this->container->set('twig', $this->twig);
        $this->container->set('admin_code.template_registry', new TemplateRegistry());
        $this->container->set('sonata.admin.pool', $pool);
        $this->container->set('sonata.admin.pool.do-not-use', $pool);
        $this->container->set('sonata.admin.breadcrumbs_builder', $breadcrumbsBuilder);
        $this->container->set('sonata.admin.breadcrumbs_builder.do-not-use', $breadcrumbsBuilder);

        if (class_exists(AdminFetcher::class)) {
            $this->container->set('sonata.admin.request.fetcher', new AdminFetcher($pool));
        }
    }
}
