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

use Runroom\Testing\TestCase\SonataAdminTestCase;
use Sonata\MediaBundle\Model\MediaInterface;

/**
 * @extends SonataAdminTestCase<MediaInterface>
 */
class MediaAdminExtensionTest extends SonataAdminTestCase
{
    /**
     * @test
     */
    public function itDoesNotHaveDisabledRoutes(): void
    {
        $this->assertAdminRoutesDoesContainRoute('browser');
        $this->assertAdminRoutesDoesContainRoute('upload');
    }

    protected function getAdminClass(): string
    {
        return 'sonata.media.admin.media';
    }
}
