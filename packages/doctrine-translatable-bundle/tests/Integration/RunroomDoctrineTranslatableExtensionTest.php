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

namespace Runroom\RunroomDoctrineTranslatableBundle\Tests\Integration;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Runroom\DoctrineTranslatableBundle\DependencyInjection\RunroomDoctrineTranslatableExtension;
use Runroom\DoctrineTranslatableBundle\EventSubscriber\TranslatableEventSubscriber;
use Runroom\DoctrineTranslatableBundle\Provider\LocaleProvider;

final class RunroomDoctrineTranslatableExtensionTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->load();
    }

    public function testItHasCoreServicesAlias(): void
    {
        $this->assertContainerBuilderHasService('runroom.doctrine_translatable.event_subscriber.translatable', TranslatableEventSubscriber::class);
        $this->assertContainerBuilderHasService('runroom.doctrine_translatable.provider.locale', LocaleProvider::class);
    }

    protected function getContainerExtensions(): array
    {
        return [new RunroomDoctrineTranslatableExtension()];
    }
}
