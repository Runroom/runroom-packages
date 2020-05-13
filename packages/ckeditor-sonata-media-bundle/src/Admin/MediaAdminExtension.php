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

namespace Runroom\CkeditorSonataMediaBundle\Admin;

use Runroom\CkeditorSonataMediaBundle\Controller\MediaAdminController;
use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Route\RouteCollection;

final class MediaAdminExtension extends AbstractAdminExtension
{
    public function configureRoutes(AdminInterface $admin, RouteCollection $collection): void
    {
        $collection->add('ckeditor_browser', 'ckeditor_browser', [
            'controller' => MediaAdminController::class . '::browser',
        ]);

        $collection->add('ckeditor_upload', 'ckeditor_upload', [
            'controller' => MediaAdminController::class . '::upload',
        ]);
    }
}
