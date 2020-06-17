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

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Argument\Token\TypeToken;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
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
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class MediaAdminControllerTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<ContainerInterface> */
    private $container;

    /** @var ObjectProphecy<BaseMediaAdmin> */
    private $admin;

    /** @var Request */
    private $request;

    /** @var ObjectProphecy<MediaManagerInterface> */
    private $mediaManager;

    /** @var ObjectProphecy<MediaPool> */
    private $mediaPool;

    /** @var ObjectProphecy<Environment> */
    private $twig;

    /** @var MediaAdminController */
    private $controller;

    protected function setUp(): void
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->admin = $this->prophesize(BaseMediaAdmin::class);
        $this->request = new Request();
        $this->mediaManager = $this->prophesize(MediaManagerInterface::class);
        $this->mediaPool = $this->prophesize(MediaPool::class);
        $this->twig = $this->prophesize(Environment::class);

        $this->configureCRUDController();
        $this->configureRequest();

        $this->controller = new MediaAdminController(
            $this->mediaManager->reveal(),
            $this->mediaPool->reveal(),
            $this->twig->reveal()
        );
        $this->controller->setContainer($this->container->reveal());
    }

    public function testBrowserAction(): void
    {
        $datagrid = $this->prophesize(DatagridInterface::class);
        $form = $this->prophesize(Form::class);
        $formView = new FormView();

        $this->configureSetFormTheme($formView, ['filterTheme']);
        $this->configureRender('@RunroomCkeditorSonataMedia/browser.html.twig', Argument::type('array'), 'renderResponse');
        $datagrid->setValue('context', null, 'another_context')->shouldBeCalled();
        $datagrid->setValue('providerName', null, 'provider')->shouldBeCalled();
        $datagrid->getResults()->willReturn([]);
        $datagrid->getForm()->willReturn($form->reveal());
        $this->mediaPool->getDefaultContext()->willReturn('context');
        $form->createView()->willReturn($formView);
        $this->admin->checkAccess('list')->shouldBeCalled();
        $this->admin->getDatagrid()->willReturn($datagrid->reveal());
        $this->admin->getPersistentParameter('context')->willReturn('another_context');
        $this->admin->getPersistentParameter('provider')->willReturn('provider');
        $this->admin->getFilterTheme()->willReturn(['filterTheme']);

        $response = $this->controller->browserAction($this->request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('renderResponse', $response->getContent());
    }

    public function testUpload(): void
    {
        $media = $this->prophesize(MediaInterface::class);
        $provider = $this->prophesize(MediaProviderInterface::class);

        $this->configureRender('@RunroomCkeditorSonataMedia/upload.html.twig', Argument::type('array'), 'renderResponse');

        $this->mediaPool->getDefaultContext()->willReturn('context');
        $this->mediaPool->getProvider('provider')->willReturn($provider->reveal());
        $this->mediaManager->create()->willReturn($media->reveal());
        $this->mediaManager->save($media->reveal())->shouldBeCalled();
        $this->admin->checkAccess('create')->shouldBeCalled();
        $this->admin->createObjectSecurity($media->reveal())->shouldBeCalled();
        $this->container->getParameter('kernel.bundles')->willReturn(['SonataMediaBundle' => true]);

        $response = $this->controller->uploadAction($this->request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('renderResponse', $response->getContent());
    }

    private function configureCRUDController(): void
    {
        $pool = $this->prophesize(AdminPool::class);
        $breadcrumbsBuilder = $this->prophesize(BreadcrumbsBuilderInterface::class);

        $this->configureGetCurrentRequest();
        $pool->getAdminByAdminCode('admin_code')->willReturn($this->admin->reveal());
        $this->container->get('sonata.admin.pool')->willReturn($pool->reveal());
        $this->container->get('sonata.admin.breadcrumbs_builder')->willReturn($breadcrumbsBuilder->reveal());
        $this->admin->getTemplate('layout')->willReturn('layout.html.twig');
        $this->admin->isChild()->willReturn(false);
        $this->admin->setRequest($this->request)->shouldBeCalled();
        $this->container->get('admin_code.template_registry')->willReturn(new TemplateRegistry());
        $this->admin->getCode()->willReturn('admin_code');
    }

    private function configureRequest(): void
    {
        // it does not work with prophesize
        $upload = $this->createMock(UploadedFile::class);

        $this->request->query->set('_sonata_admin', 'admin_code');
        $this->request->query->set('provider', 'provider');
        $this->request->files->set('upload', $upload);
        $this->request->setMethod('POST');
    }

    private function configureGetCurrentRequest(): void
    {
        $requestStack = new RequestStack();
        $requestStack->push($this->request);

        $this->container->has('request_stack')->willReturn(true);
        $this->container->get('request_stack')->willReturn($requestStack);
    }

    /** @param string[] $formTheme */
    private function configureSetFormTheme(FormView $formView, array $formTheme): void
    {
        $twigRenderer = $this->prophesize(FormRenderer::class);

        $this->twig->getRuntime(FormRenderer::class)->willReturn($twigRenderer->reveal());

        $twigRenderer->setTheme($formView, $formTheme)->shouldBeCalled();
    }

    /** @param TypeToken $data */
    private function configureRender(string $template, $data, string $rendered): void
    {
        $this->admin->getPersistentParameters()->willReturn(['param' => 'param']);
        $this->twig->render($template, $data)->willReturn($rendered);
        $this->container->has('templating')->willReturn(false);
        $this->container->has('twig')->willReturn(true);
        $this->container->get('twig')->willReturn($this->twig->reveal());
        $this->container->get('sonata.media.pool')->willReturn($this->mediaPool->reveal());
    }
}
