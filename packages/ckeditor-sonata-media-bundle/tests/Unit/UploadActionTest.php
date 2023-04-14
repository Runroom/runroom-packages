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
use Runroom\CkeditorSonataMediaBundle\Action\UploadAction;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\Pool as AdminPool;
use Sonata\AdminBundle\Request\AdminFetcher;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Model\MediaManagerInterface;
use Sonata\MediaBundle\Provider\MediaProviderInterface;
use Sonata\MediaBundle\Provider\Pool as MediaPool;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

class UploadActionTest extends TestCase
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

    private MediaPool $mediaPool;

    /**
     * @var MockObject&Environment
     */
    private $twig;

    private UploadAction $action;

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

        $this->action = new UploadAction(
            new AdminFetcher(new AdminPool($this->container, [
                'admin.code' => 'admin_code',
            ])),
            $this->mediaManager,
            $this->mediaPool
        );
        $this->action->setContainer($this->container);
    }

    public function testUploadAction(): void
    {
        $media = $this->createStub(MediaInterface::class);
        $provider = $this->createStub(MediaProviderInterface::class);

        $this->configureRender('@RunroomCkeditorSonataMedia/upload.html.twig', 'renderResponse');

        $this->mediaPool->addProvider('provider', $provider);

        $this->mediaManager->expects(static::once())->method('create')->willReturn($media);
        $this->mediaManager->expects(static::once())->method('save')->with($media);
        $this->admin->expects(static::once())->method('checkAccess')->with('create');
        $this->admin->expects(static::once())->method('createObjectSecurity')->with($media);

        $response = ($this->action)($this->request);

        static::assertSame('renderResponse', $response->getContent());
    }

    public function testUploadActionThrowsWhenNoPostMethod(): void
    {
        $this->request->setMethod('GET');

        $this->expectException(NotFoundHttpException::class);

        ($this->action)($this->request);
    }

    private function configureAdmin(): void
    {
        $this->admin->expects(static::once())->method('isChild')->willReturn(false);
        $this->admin->expects(static::once())->method('setRequest')->with($this->request);
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
     * @todo: Simplify when dropping support for sonata-project/admin-bundle 3
     */
    private function configureRender(string $template, string $rendered): void
    {
        $this->admin->method('getPersistentParameters')->willReturn([
            'param' => 'param',
            'context' => 'another_context',
        ]);
        $this->twig->method('render')->with($template, static::isType('array'))->willReturn($rendered);
    }

    private function configureContainer(): void
    {
        $this->container->set('admin_code', $this->admin);
        $this->container->set('twig', $this->twig);
    }
}
