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
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\MediaBundle\Model\MediaInterface;

/** @extends AbstractAdminExtension<MediaInterface> */
final class MediaAdminExtension extends AbstractAdminExtension
{
    /**
     * @todo: Simplify this when dropping support for Sonata 3
     *
     * @param RouteCollection|RouteCollectionInterface $collection
     */
    public function configureRoutes(AdminInterface $admin, object $collection): void
    {
        $collection->add('ckeditor_browser', 'ckeditor_browser', [
            '_controller' => MediaAdminController::class . '::browserAction',
        ]);

        $collection->add('ckeditor_upload', 'ckeditor_upload', [
            '_controller' => MediaAdminController::class . '::uploadAction',
        ]);
    }
}
