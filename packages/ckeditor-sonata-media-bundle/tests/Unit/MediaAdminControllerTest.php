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
use Sonata\AdminBundle\Admin\BreadcrumbsBuilderInterface;
use Sonata\AdminBundle\Admin\Pool as AdminPool;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Templating\TemplateRegistry;
use Sonata\MediaBundle\Admin\BaseMediaAdmin;
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

    /** @var MockObject&BaseMediaAdmin */
    private $admin;

    private Request $request;

    /** @var MockObject&MediaManagerInterface */
    private $mediaManager;

    /** @var MockObject&MediaPool */
    private $mediaPool;

    /** @var MockObject&Environment */
    private $twig;

    private MediaAdminController $controller;

    protected function setUp(): void
    {
        $this->container = new Container();
        $this->admin = $this->createMock(BaseMediaAdmin::class);
        $this->request = new Request();
        $this->mediaManager = $this->createMock(MediaManagerInterface::class);
        $this->mediaPool = $this->createMock(MediaPool::class);
        $this->twig = $this->createMock(Environment::class);

        $this->configureCRUDController();
        $this->configureRequest();
        $this->configureContainer();

        $this->controller = new MediaAdminController(
            $this->mediaManager,
            $this->mediaPool,
            $this->twig
        );
        $this->controller->setContainer($this->container);
    }

    /** @test */
    public function browserAction(): void
    {
        $datagrid = $this->createMock(DatagridInterface::class);
        $form = $this->createStub(Form::class);
        $formView = new FormView();
        $media = new Media();
        $media->setId(1);
        $media->setContext('context');

        $this->configureSetFormTheme($formView, ['filterTheme']);
        $this->configureRender('@RunroomCkeditorSonataMedia/browser.html.twig', 'renderResponse');
        $datagrid->expects(self::exactly(2))->method('setValue')->withConsecutive(
            ['context', null, 'another_context'],
            ['providerName', null, null]
        );
        $datagrid->method('getResults')->willReturn([new Media()]);
        $datagrid->method('getForm')->willReturn($form);
        $this->mediaPool->method('getFormatNamesByContext')->willReturn('');
        $form->method('createView')->willReturn($formView);
        $this->admin->expects(self::once())->method('checkAccess')->with('list');
        $this->admin->method('getDatagrid')->willReturn($datagrid);
        $this->admin->method('getPersistentParameter')->willReturnMap([
            ['context', 'another_context'],
        ]);
        $this->admin->method('getFilterTheme')->willReturn(['filterTheme']);

        $response = $this->controller->browserAction($this->request);

        self::assertSame('renderResponse', $response->getContent());
    }

    /** @test */
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
        $datagrid->expects(self::exactly(2))->method('setValue')->withConsecutive(
            ['context', null, 'context'],
            ['providerName', null, null]
        );
        $datagrid->method('getResults')->willReturn([]);
        $datagrid->method('getForm')->willReturn($form);
        $this->mediaPool->method('getFormatNamesByContext')->willReturn('');
        $form->method('createView')->willReturn($formView);
        $this->admin->expects(self::once())->method('checkAccess')->with('list');
        $this->admin->method('getDatagrid')->willReturn($datagrid);
        $this->admin->method('getFilterTheme')->willReturn(['filterTheme']);

        $response = $this->controller->browserAction($this->request);

        self::assertSame('renderResponse', $response->getContent());
    }

    /** @test */
    public function uploadAction(): void
    {
        $media = $this->createStub(MediaInterface::class);
        $provider = $this->createStub(MediaProviderInterface::class);

        $this->configureRender('@RunroomCkeditorSonataMedia/upload.html.twig', 'renderResponse');

        $this->mediaPool->method('getDefaultContext')->willReturn('context');
        $this->mediaPool->method('getProvider')->with('provider')->willReturn($provider);
        $this->mediaManager->method('create')->willReturn($media);
        $this->mediaManager->expects(self::once())->method('save')->with($media);
        $this->admin->expects(self::once())->method('checkAccess')->with('create');
        $this->admin->expects(self::once())->method('createObjectSecurity')->with($media);

        $response = $this->controller->uploadAction($this->request);

        self::assertSame('renderResponse', $response->getContent());
    }

    /** @test */
    public function uploadActionThrowsWhenNoPostMethod(): void
    {
        $this->request->setMethod('GET');

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Not Found');

        $this->controller->uploadAction($this->request);
    }

    private function configureCRUDController(): void
    {
        $this->admin->method('getTemplate')->with('layout')->willReturn('layout.html.twig');
        $this->admin->method('isChild')->willReturn(false);
        $this->admin->expects(self::once())->method('setRequest')->with($this->request);
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

    /** @param string[] $formTheme */
    private function configureSetFormTheme(FormView $formView, array $formTheme): void
    {
        $twigRenderer = $this->createMock(FormRenderer::class);

        $this->twig->method('getRuntime')->with(FormRenderer::class)->willReturn($twigRenderer);

        $twigRenderer->expects(self::once())->method('setTheme')->with($formView, $formTheme);
    }

    private function configureRender(string $template, string $rendered): void
    {
        $this->admin->method('getPersistentParameters')->willReturn(['param' => 'param']);
        $this->twig->method('render')->with($template, self::isType('array'))->willReturn($rendered);
    }

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
    }
}
