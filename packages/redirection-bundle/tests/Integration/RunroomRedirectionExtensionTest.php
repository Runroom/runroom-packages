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
use Runroom\RedirectionBundle\Listener\RedirectListener;
use Runroom\RedirectionBundle\Repository\RedirectRepository;

class RunroomRedirectionExtensionTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->load();
    }

    /** @test */
    public function itHasCoreServicesAlias(): void
    {
        $this->assertContainerBuilderHasService(RedirectAdmin::class);
        $this->assertContainerBuilderHasService(RedirectListener::class);
        $this->assertContainerBuilderHasService(RedirectRepository::class);
    }

    protected function getContainerExtensions(): array
    {
        return [new RunroomRedirectionExtension()];
    }
}
