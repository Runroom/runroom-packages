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

use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\MediaBundle\Model\MediaInterface;

/**
 * @extends AbstractAdminExtension<MediaInterface>
 */
final class MediaAdminExtension extends AbstractAdminExtension
{
    /**
     * @todo: Simplify this when dropping support for Sonata 3
     *
     * @param RouteCollection|RouteCollectionInterface $collection
     */
    public function configureRoutes(AdminInterface $admin, object $collection): void
    {
        $collection->add('browser', 'browser', [
            '_controller' => 'runroom.ckeditor_sonata_media.action.browser',
        ]);

        $collection->add('upload', 'upload', [
            '_controller' => 'runroom.ckeditor_sonata_media.action.upload',
        ]);
    }

    public function configureBatchActions(AdminInterface $admin, array $actions): array
    {
        return $admin->isCurrentRoute('browser') ? [] : $actions;
    }
}
