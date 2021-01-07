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
use Sonata\AdminBundle\Admin\BreadcrumbsBuilderInterface;
use Sonata\AdminBundle\Admin\Pool as AdminPool;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Templating\TemplateRegistry;
use Sonata\MediaBundle\Admin\BaseMediaAdmin;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Model\MediaManagerInterface;
use Sonata\MediaBundle\Provider\MediaProviderInterface;
use Sonata\MediaBundle\Provider\Pool as MediaPool;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class MediaAdminControllerTest extends TestCase
{
    /** @var MockObject&ContainerInterface */
    private $container;

    /** @var MockObject&BaseMediaAdmin */
    private $admin;

    /** @var Request */
    private $request;

    /** @var MockObject&MediaManagerInterface */
    private $mediaManager;

    /** @var MockObject&MediaPool */
    private $mediaPool;

    /** @var MockObject&Environment */
    private $twig;

    /** @var MediaAdminController */
    private $controller;

    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
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

        $this->configureSetFormTheme($formView, ['filterTheme']);
        $this->configureRender('@RunroomCkeditorSonataMedia/browser.html.twig', 'renderResponse');
        $datagrid->expects(self::exactly(2))->method('setValue')->withConsecutive(
            ['context', null, 'another_context'],
            ['providerName', null, 'provider']
        );
        $datagrid->method('getResults')->willReturn([]);
        $datagrid->method('getForm')->willReturn($form);
        $this->mediaPool->method('getDefaultContext')->willReturn('context');
        $form->method('createView')->willReturn($formView);
        $this->admin->expects(self::once())->method('checkAccess')->with('list');
        $this->admin->method('getDatagrid')->willReturn($datagrid);
        $this->admin->method('getPersistentParameter')->willReturnMap([
            ['context', 'another_context'],
            ['provider', 'provider'],
        ]);
        $this->admin->method('getFilterTheme')->willReturn(['filterTheme']);

        $response = $this->controller->browserAction($this->request);

        self::assertSame('renderResponse', $response->getContent());
    }

    /** @test */
    public function upload(): void
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
        $pool = $this->createMock(AdminPool::class);
        $breadcrumbsBuilder = $this->createStub(BreadcrumbsBuilderInterface::class);

        $pool->method('getAdminByAdminCode')->with('admin_code')->willReturn($this->admin);

        $requestStack = new RequestStack();
        $requestStack->push($this->request);

        $this->container->method('has')->willReturnMap([
            ['request_stack', true],
            ['templating', false],
            ['twig', true],
        ]);
        $this->container->method('get')->willReturnMap([
            ['sonata.admin.pool', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $pool],
            ['sonata.admin.breadcrumbs_builder', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $breadcrumbsBuilder],
            ['request_stack', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $requestStack],
            ['twig', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->twig],
            ['sonata.media.pool', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mediaPool],
            ['admin_code.template_registry', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, new TemplateRegistry()],
        ]);
        $this->container->method('getParameter')->with('kernel.bundles')->willReturn(['SonataMediaBundle' => true]);
    }
}
