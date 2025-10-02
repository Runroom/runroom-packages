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
use Runroom\CkeditorSonataMediaBundle\Action\BrowserAction;
use Runroom\CkeditorSonataMediaBundle\Action\UploadAction;
use Runroom\CkeditorSonataMediaBundle\Admin\MediaAdminExtension;
use Runroom\CkeditorSonataMediaBundle\DependencyInjection\RunroomCkeditorSonataMediaExtension;

final class RunroomCkeditorSonataMediaExtensionTest extends AbstractExtensionTestCase
{
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->load();
    }

    public function testItHasCoreServicesAlias(): void
    {
        $this->assertContainerBuilderHasService('runroom.ckeditor_sonata_media.action.browser', BrowserAction::class);
        $this->assertContainerBuilderHasService('runroom.ckeditor_sonata_media.action.upload', UploadAction::class);
        $this->assertContainerBuilderHasService('runroom.ckeditor_sonata_media.admin.media_admin', MediaAdminExtension::class);
    }

    protected function getContainerExtensions(): array
    {
        return [new RunroomCkeditorSonataMediaExtension()];
    }
}
