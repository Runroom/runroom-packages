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
use Runroom\CkeditorSonataMediaBundle\Action\BrowserAction;
use Runroom\CkeditorSonataMediaBundle\Tests\App\Entity\Media;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\Pool as AdminPool;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Request\AdminFetcher;
use Sonata\MediaBundle\Provider\Pool as MediaPool;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

final class BrowserActionTest extends TestCase
{
    private Container $container;

    /**
     * @var MockObject&AdminInterface<object>
     */
    private MockObject&AdminInterface $admin;

    private Request $request;
    private MediaPool $mediaPool;
    private MockObject&Environment $twig;
    private BrowserAction $action;

    protected function setUp(): void
    {
        $this->container = new Container();
        $this->admin = $this->createMock(AdminInterface::class);
        $this->request = new Request();
        $this->mediaPool = new MediaPool('context');
        $this->twig = $this->createMock(Environment::class);

        $this->configureAdmin();
        $this->configureRequest();
        $this->configureContainer();

        $this->action = new BrowserAction(
            $this->twig,
            new AdminFetcher(new AdminPool($this->container, [
                'admin.code' => 'admin_code',
            ])),
            $this->mediaPool
        );
        $this->action->setContainer($this->container);
    }

    public function testBrowserAction(): void
    {
        $datagrid = $this->createMock(DatagridInterface::class);
        $form = $this->createMock(Form::class);
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
        $datagrid->expects(static::exactly(2))->method('setValue');
        $datagrid->expects(static::once())->method('getResults')->willReturn([new Media(), $media, $media2]);
        $datagrid->expects(static::once())->method('getForm')->willReturn($form);
        $form->expects(static::once())->method('createView')->willReturn($formView);
        $this->admin->expects(static::once())->method('checkAccess')->with('list');
        $this->admin->expects(static::once())->method('getDatagrid')->willReturn($datagrid);
        $this->admin->expects(static::once())->method('getFilterTheme')->willReturn(['filterTheme']);

        $response = ($this->action)($this->request);

        static::assertSame('renderResponse', $response->getContent());
    }

    public function testBrowserActionWithFilters(): void
    {
        $datagrid = $this->createMock(DatagridInterface::class);
        $form = $this->createMock(Form::class);
        $formView = new FormView();

        $this->request->query->set('filter', ['context' => [
            'value' => 'context',
        ]]);

        $this->configureSetFormTheme($formView, ['filterTheme']);
        $this->configureRender('@RunroomCkeditorSonataMedia/browser.html.twig', 'renderResponse');
        $datagrid->expects(static::exactly(2))->method('setValue');
        $datagrid->expects(static::once())->method('getResults')->willReturn([]);
        $datagrid->expects(static::once())->method('getForm')->willReturn($form);
        $form->expects(static::once())->method('createView')->willReturn($formView);
        $this->admin->expects(static::once())->method('checkAccess')->with('list');
        $this->admin->expects(static::once())->method('getDatagrid')->willReturn($datagrid);
        $this->admin->expects(static::once())->method('getFilterTheme')->willReturn(['filterTheme']);

        $response = ($this->action)($this->request);

        static::assertSame('renderResponse', $response->getContent());
    }

    private function configureAdmin(): void
    {
        $this->admin->expects(static::once())->method('isChild')->willReturn(false);
        $this->admin->expects(static::once())->method('setRequest')->with($this->request);
    }

    private function configureRequest(): void
    {
        $this->request->query->set('_sonata_admin', 'admin_code');
    }

    /**
     * @param string[] $formTheme
     */
    private function configureSetFormTheme(FormView $formView, array $formTheme): void
    {
        $twigRenderer = $this->createMock(FormRenderer::class);

        $this->twig->expects(static::once())->method('getRuntime')->with(FormRenderer::class)->willReturn($twigRenderer);
        $twigRenderer->expects(static::once())->method('setTheme')->with($formView, $formTheme);
    }

    private function configureRender(string $template, string $rendered): void
    {
        $this->twig->expects(static::once())->method('render')->with($template, static::isArray())->willReturn($rendered);
    }

    private function configureContainer(): void
    {
        $this->container->set('admin_code', $this->admin);
        $this->container->set('twig', $this->twig);
    }
}
