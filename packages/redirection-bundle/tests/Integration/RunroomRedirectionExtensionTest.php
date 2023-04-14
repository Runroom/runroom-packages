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

namespace Runroom\RedirectionBundle\Tests\Integration;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Runroom\RedirectionBundle\Admin\RedirectAdmin;
use Runroom\RedirectionBundle\DependencyInjection\RunroomRedirectionExtension;
use Runroom\RedirectionBundle\EventSubscriber\AutomaticRedirectSubscriber;
use Runroom\RedirectionBundle\EventSubscriber\RedirectSubscriber;
use Runroom\RedirectionBundle\Repository\RedirectRepository;

class RunroomRedirectionExtensionTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->container->setParameter('kernel.bundles', ['SonataAdminBundle' => true]);

        $this->load(['enable_automatic_redirections' => true]);
    }

    public function testItHasCoreServicesAlias(): void
    {
        $this->assertContainerBuilderHasService('runroom.redirection.admin.redirect', RedirectAdmin::class);
        $this->assertContainerBuilderHasService('runroom.redirection.event_subscriber.redirect', RedirectSubscriber::class);
        $this->assertContainerBuilderHasService('runroom.redirection.event_subscriber.automatic_redirect', AutomaticRedirectSubscriber::class);
        $this->assertContainerBuilderHasService(RedirectRepository::class);
    }

    protected function getContainerExtensions(): array
    {
        return [new RunroomRedirectionExtension()];
    }
}
