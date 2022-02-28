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

namespace Runroom\CkeditorSonataMediaBundle\Tests\Integration;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Runroom\CkeditorSonataMediaBundle\Admin\MediaAdminExtension;
use Runroom\CkeditorSonataMediaBundle\Controller\MediaAdminController;
use Runroom\CkeditorSonataMediaBundle\DependencyInjection\RunroomCkeditorSonataMediaExtension;

class RunroomCkeditorSonataMediaExtensionTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->load();
    }

    /**
     * @test
     */
    public function itHasCoreServicesAlias(): void
    {
        $this->assertContainerBuilderHasService(MediaAdminController::class);
        $this->assertContainerBuilderHasService(MediaAdminExtension::class);
    }

    protected function getContainerExtensions(): array
    {
        return [new RunroomCkeditorSonataMediaExtension()];
    }
}
